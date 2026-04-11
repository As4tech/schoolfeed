<?php

namespace App\Exports;

use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyRevenueExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected string $month;
    protected ?string $class;
    protected ?int $schoolId;

    public function __construct(string $month, ?string $class, ?int $schoolId)
    {
        $this->month = $month;
        $this->class = $class;
        $this->schoolId = $schoolId;
    }

    public function collection()
    {
        $startDate = Carbon::parse($this->month)->startOfMonth();
        $endDate = Carbon::parse($this->month)->endOfMonth();

        $query = Payment::with(['guardian', 'school', 'items.student'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($this->schoolId, function ($q) {
                $q->where('school_id', $this->schoolId);
            });

        if ($this->class) {
            $query->whereHas('items.student', function ($q) {
                $q->where('grade', $this->class);
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Payment Date',
            'Reference',
            'School',
            'Guardian',
            'Student(s)',
            'Status',
            'Total Amount (GH₵)',
            'Platform Fee (GH₵)',
            'School Amount (GH₵)',
            'Payment Method',
        ];
    }

    public function map($payment): array
    {
        $students = $payment->items->map(fn($item) => $item->student->full_name)->join(', ');

        return [
            $payment->created_at->format('Y-m-d H:i:s'),
            $payment->reference,
            $payment->school->name,
            $payment->guardian->name ?? 'N/A',
            $students,
            ucfirst($payment->status),
            number_format($payment->total_amount, 2),
            number_format($payment->platform_fee, 2),
            number_format($payment->school_amount, 2),
            ucfirst($payment->payment_method),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
