<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\MealRecipe;
use Illuminate\Http\Request;

class MealRecipeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = MealRecipe::with('item');
        if (!$user->hasRole('Super Admin')) {
            $query->where('school_id', $user->school_id);
        }
        if ($meal = $request->get('meal')) {
            $query->where('meal_name', 'like', "%{$meal}%");
        }
        $recipes = $query->orderBy('meal_name')->paginate(20)->withQueryString();

        $items = InventoryItem::when(!$user->hasRole('Super Admin'), fn($q) => $q->where('school_id', $user->school_id))
            ->orderBy('name')->get();

        return view('admin.recipes.index', compact('recipes', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meal_name' => 'required|string|max:255',
            'item_id' => 'required|exists:inventory_items,id',
            'quantity_per_student' => 'required|numeric|min:0.0001',
        ]);

        $user = auth()->user();
        $itemSchoolId = InventoryItem::findOrFail($validated['item_id'])->school_id;
        if (!$user->hasRole('Super Admin') && $itemSchoolId !== $user->school_id) {
            abort(403);
        }

        MealRecipe::create([
            'school_id' => $user->school_id,
            'meal_name' => $validated['meal_name'],
            'item_id' => $validated['item_id'],
            'quantity_per_student' => $validated['quantity_per_student'],
        ]);

        return redirect()->route('admin.recipes.index')->with('success', 'Recipe ingredient added.');
    }

    public function destroy(MealRecipe $recipe)
    {
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && $recipe->school_id !== $user->school_id) {
            abort(403);
        }
        $recipe->delete();
        return redirect()->route('admin.recipes.index')->with('success', 'Recipe ingredient removed.');
    }
}
