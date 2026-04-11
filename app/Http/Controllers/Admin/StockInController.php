<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $items = InventoryItem::when(!$user->hasRole('Super Admin'), fn($q) => $q->where('school_id', $user->school_id))
            ->orderBy('name')->get();
        return view('admin.inventory.stock-in.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.001',
            'cost_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $user = auth()->user();
        $item = InventoryItem::findOrFail($validated['item_id']);
        if (!$user->hasRole('Super Admin') && $item->school_id !== $user->school_id) {
            abort(403);
        }

        DB::transaction(function () use ($item, $validated) {
            // Increase stock
            $item->increment('quantity', $validated['quantity']);
            // Record stock in
            StockIn::create([
                'item_id' => $item->id,
                'quantity' => $validated['quantity'],
                'cost_price' => $validated['cost_price'] ?? 0,
                'supplier' => $validated['supplier'] ?? null,
                'date' => $validated['date'],
            ]);
        });

        return redirect()->route('admin.inventory.items.index')->with('success', 'Stock added.');
    }
}
