<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $items = InventoryItem::when(!$user->hasRole('Super Admin'), fn($q) => $q->where('school_id', $user->school_id))
            ->orderBy('name')->get();
        return view('admin.inventory.stock-out.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|numeric|min:0.001',
            'reason' => 'required|string|max:50',
            'date' => 'required|date',
        ]);

        $user = auth()->user();
        $item = InventoryItem::findOrFail($validated['item_id']);
        if (!$user->hasRole('Super Admin') && $item->school_id !== $user->school_id) {
            abort(403);
        }

        // Prevent negative stock
        if ((float)$item->quantity < (float)$validated['quantity']) {
            return back()->withErrors(['quantity' => 'Insufficient stock. Available: ' . $item->quantity . ' ' . $item->unit])->withInput();
        }

        DB::transaction(function () use ($item, $validated) {
            // Decrease stock
            $item->decrement('quantity', $validated['quantity']);
            // Record stock out
            StockOut::create([
                'item_id' => $item->id,
                'quantity' => $validated['quantity'],
                'reason' => $validated['reason'],
                'date' => $validated['date'],
            ]);
        });

        return redirect()->route('admin.inventory.items.index')->with('success', 'Stock deducted.');
    }
}
