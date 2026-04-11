<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Student;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = Payment::with(['guardian', 'items.student'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $guardians = Guardian::orderBy('name')->get();
        $students = Student::with('parent')->orderBy('first_name')->get();
        
        return view('admin.payments.create', compact('guardians', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guardian_id' => 'required|exists:guardians,id',
            'notes' => 'nullable|string',
            'students' => 'required|array|min:1',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.amount' => 'required|numeric|min:0.01',
            'students.*.description' => 'nullable|string',
        ]);

        $guardian = Guardian::findOrFail($validated['guardian_id']);
        $school = auth()->user()->school;

        // Calculate totals
        $feedingTotal = collect($validated['students'])->sum('amount');
        $platformFee = round($feedingTotal * 0.01, 2); // 1% platform fee
        $totalAmount = $feedingTotal + $platformFee;

        // Create pending payment record
        $reference = 'PAY-' . strtoupper(Str::random(8));
        $payment = Payment::create([
            'school_id' => $school->id,
            'guardian_id' => $validated['guardian_id'],
            'reference' => $reference,
            'total_amount' => $totalAmount,
            'platform_fee' => $platformFee,
            'school_amount' => $feedingTotal,
            'status' => 'pending',
            'payment_method' => 'paystack',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Create payment items
        foreach ($validated['students'] as $studentData) {
            PaymentItem::create([
                'payment_id' => $payment->id,
                'student_id' => $studentData['student_id'],
                'amount' => $studentData['amount'],
                'description' => $studentData['description'] ?? 'Feeding fee',
            ]);
        }

        // Initialize Paystack transaction
        $paystackData = [
            'email' => $guardian->email ?? $guardian->user->email ?? 'parent@example.com',
            'amount' => $totalAmount,
            'platform_fee' => $platformFee,
            'reference' => $reference,
            'callback_url' => route('payment.callback'),
            'payment_id' => $payment->id,
            'guardian_id' => $guardian->id,
            'school_id' => $school->id,
            'school_name' => $school->name,
            'students' => collect($validated['students'])->map(function ($item) {
                $student = Student::find($item['student_id']);
                return [
                    'id' => $item['student_id'],
                    'name' => $student?->full_name ?? 'Unknown',
                    'amount' => $item['amount'],
                ];
            })->toArray(),
            'subaccount_code' => $school->paystack_subaccount_code,
        ];

        $result = $this->paymentService->initialize($paystackData);

        if ($result['success']) {
            return redirect()->away($result['authorization_url']);
        }

        // Failed to initialize - update payment status
        $payment->update(['status' => 'failed']);

        return redirect()->back()
            ->with('error', $result['message'] ?? 'Failed to initialize payment. Please try again.');
    }

    /**
     * Handle Paystack callback
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->get('reference');

        if (!$reference) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid payment reference.');
        }

        $payment = Payment::where('reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('dashboard')
                ->with('error', 'Payment not found.');
        }

        // Verify transaction with Paystack
        $result = $this->paymentService->verify($reference);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);
            return redirect()->route('dashboard')
                ->with('error', 'Payment verification failed: ' . $result['message']);
        }

        // Check payment status
        if ($result['status'] === 'success') {
            $payment->update([
                'status' => 'completed',
                'paid_at' => $result['paid_at'] ?? now(),
                'payment_method' => $result['channel'] ?? 'paystack',
            ]);

            // Send notification to the guardian/parent
            if ($payment->guardian && $payment->guardian->user) {
                $payment->guardian->user->notify(new \App\Notifications\PaymentSuccessNotification($payment));
            }

            Log::info('Payment completed successfully', [
                'payment_id' => $payment->id,
                'reference' => $reference,
                'amount' => $result['amount'],
            ]);

            return redirect()->route('payment.success', $payment)
                ->with('success', 'Payment completed successfully!');
        }

        // Payment failed or abandoned
        $payment->update(['status' => 'failed']);

        Log::warning('Payment failed or abandoned', [
            'payment_id' => $payment->id,
            'reference' => $reference,
            'status' => $result['status'],
        ]);

        return redirect()->route('dashboard')
            ->with('error', 'Payment was not completed. Please try again.');
    }

    /**
     * Payment success page
     */
    public function success(Payment $payment)
    {
        $payment->load(['guardian', 'items.student', 'school']);
        
        return view('payments.success', compact('payment'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['guardian', 'items.student', 'school']);
        
        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        // Only allow editing pending payments
        if ($payment->status !== 'pending') {
            return redirect()->route('admin.payments.show', $payment)
                ->with('error', 'Only pending payments can be edited.');
        }
        
        $guardians = Guardian::orderBy('name')->get();
        $students = Student::with('parent')->orderBy('first_name')->get();
        
        return view('admin.payments.edit', compact('payment', 'guardians', 'students'));
    }

    public function update(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->route('admin.payments.show', $payment)
                ->with('error', 'Only pending payments can be updated.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment->update($validated);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        // Delete related items first
        $payment->items()->delete();
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function getGuardianStudents(Guardian $guardian)
    {
        $students = $guardian->students()->with('feedingPlans')->get();
        
        return response()->json([
            'students' => $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'grade' => $student->grade,
                    'active_plans' => $student->feedingPlans->where('pivot.status', 'active')->count(),
                ];
            }),
        ]);
    }

    public function myPayments()
    {
        $user = auth()->user();
        $guardian = $user?->guardian;

        if (!$guardian) {
            return view('admin.payments.mine', [
                'payments' => collect(),
            ]);
        }

        $payments = Payment::where('guardian_id', $guardian->id)
            ->with(['items.student', 'school'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.payments.mine', compact('payments'));
    }
}
