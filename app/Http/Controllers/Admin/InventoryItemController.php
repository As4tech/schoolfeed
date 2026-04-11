<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = InventoryItem::query();
        if (!$user->hasRole('Super Admin')) {
            $query->where('school_id', $user->school_id);
        }
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        $items = $query->orderBy('name')->paginate(15)->withQueryString();
        $lowStock = (clone $query)->whereColumn('quantity', '<=', 'min_level')->count();
        return view('admin.inventory.items.index', compact('items', 'lowStock'));
    }

    public function create()
    {
        return view('admin.inventory.items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'quantity' => 'nullable|numeric|min:0',
            'min_level' => 'nullable|numeric|min:0',
        ]);

        InventoryItem::create([
            'school_id' => auth()->user()->school_id,
            'name' => $validated['name'],
            'unit' => $validated['unit'],
            'quantity' => $validated['quantity'] ?? 0,
            'min_level' => $validated['min_level'] ?? 0,
        ]);

        return redirect()->route('admin.inventory.items.index')->with('success', 'Item created.');
    }

    public function edit(InventoryItem $item)
    {
        $this->authorizeItem($item);
        return view('admin.inventory.items.edit', compact('item'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $this->authorizeItem($item);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'min_level' => 'nullable|numeric|min:0',
        ]);
        $item->update($validated);
        return redirect()->route('admin.inventory.items.index')->with('success', 'Item updated.');
    }

    public function destroy(InventoryItem $item)
    {
        $this->authorizeItem($item);
        $item->delete();
        return redirect()->route('admin.inventory.items.index')->with('success', 'Item deleted.');
    }

    private function authorizeItem(InventoryItem $item): void
    {
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && $item->school_id !== $user->school_id) {
            abort(403);
        }
    }
}
