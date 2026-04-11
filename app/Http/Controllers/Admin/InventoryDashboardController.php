<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;

class InventoryDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $itemsQuery = InventoryItem::query();
        if (!$user->hasRole('Super Admin')) {
            $itemsQuery->where('school_id', $user->school_id);
        }

        $totalItems = (clone $itemsQuery)->count();
        $lowStockCount = (clone $itemsQuery)->whereColumn('quantity', '<=', 'min_level')->count();
        $totalQuantity = (clone $itemsQuery)->sum('quantity');
        $lowStockItems = (clone $itemsQuery)
            ->whereColumn('quantity', '<=', 'min_level')
            ->orderByRaw('(quantity - min_level) asc')
            ->limit(8)
            ->get();

        $stockInQuery = StockIn::with('item');
        $stockOutQuery = StockOut::with('item');
        if (!$user->hasRole('Super Admin')) {
            $stockInQuery->whereHas('item', fn($q) => $q->where('school_id', $user->school_id));
            $stockOutQuery->whereHas('item', fn($q) => $q->where('school_id', $user->school_id));
        }
        $recentIns = $stockInQuery->orderBy('date', 'desc')->limit(10)->get();
        $recentOuts = $stockOutQuery->orderBy('date', 'desc')->limit(10)->get();

        return view('admin.inventory.dashboard.index', compact(
            'totalItems', 'lowStockCount', 'totalQuantity', 'lowStockItems', 'recentIns', 'recentOuts'
        ));
    }
}
