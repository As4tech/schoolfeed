<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\KitchenUsage;
use App\Models\KitchenUsageItem;
use App\Models\MealRecipe;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitchenUsageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = KitchenUsage::with('items.item')->orderBy('date', 'desc');
        if (!$user->hasRole('Super Admin')) {
            $query->where('school_id', $user->school_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        $usages = $query->paginate(15)->withQueryString();
        return view('admin.kitchen.usages.index', compact('usages'));
    }

    public function create()
    {
        // Collect meal names from existing recipes for convenience
        $user = auth()->user();
        $recipes = MealRecipe::when(!$user->hasRole('Super Admin'), fn($q) => $q->where('school_id', $user->school_id))
            ->orderBy('meal_name')
            ->get()
            ->groupBy('meal_name');
        return view('admin.kitchen.usages.create', compact('recipes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'students_fed' => 'required|integer|min:0',
            'meal_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $user = auth()->user();
        $schoolId = $user->school_id;

        // Fetch recipe rows for this meal
        $recipes = MealRecipe::where('meal_name', $validated['meal_name'])
            ->when(!$user->hasRole('Super Admin'), fn($q) => $q->where('school_id', $schoolId))
            ->get();

        if ($recipes->isEmpty()) {
            return back()->withErrors(['meal_name' => 'No recipe defined for this meal. Add ingredients in Recipes.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validated, $recipes, $schoolId) {
                // Create usage record
                $usage = KitchenUsage::create([
                    'school_id' => $schoolId,
                    'date' => $validated['date'],
                    'notes' => $validated['notes'] ?? null,
                    'students_fed' => $validated['students_fed'],
                ]);

                foreach ($recipes as $recipe) {
                    $item = InventoryItem::lockForUpdate()->findOrFail($recipe->item_id);
                    if ($item->school_id !== $schoolId) {
                        abort(403);
                    }
                    $qty = round($recipe->quantity_per_student * $validated['students_fed'], 3);
                    if ($qty <= 0) continue;
                    // Prevent negative stock
                    if ((float)$item->quantity < (float)$qty) {
                        throw new \RuntimeException("Insufficient stock for {$item->name}. Needed {$qty} {$item->unit}, available {$item->quantity} {$item->unit}.");
                    }
                    // Deduct
                    $item->decrement('quantity', $qty);
                    // Log stock out as cooking
                    StockOut::create([
                        'item_id' => $item->id,
                        'quantity' => $qty,
                        'reason' => 'cooking',
                        'date' => $validated['date'],
                    ]);
                    // Link to usage
                    KitchenUsageItem::create([
                        'usage_id' => $usage->id,
                        'item_id' => $item->id,
                        'quantity' => $qty,
                    ]);
                }
            });
        } catch (\RuntimeException $ex) {
            return back()->withErrors(['quantity' => $ex->getMessage()])->withInput();
        }

        return redirect()->route('admin.kitchen.usages.index')->with('success', 'Kitchen usage recorded and stock deducted.');
    }
}
