<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\KitchenUsageItem;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryReportsController extends Controller
{
    // Daily consumption of ingredients (from kitchen_usage_items)
    public function dailyConsumption(Request $request)
    {
        $user = auth()->user();
        $start = $request->date('start', now()->startOfMonth());
        $end = $request->date('end', now()->endOfMonth());

        $query = KitchenUsageItem::query()
            ->select([
                DB::raw('DATE(kitchen_usages.date) as usage_date'),
                'inventory_items.id as item_id',
                'inventory_items.name as item_name',
                'inventory_items.unit as unit',
                DB::raw('SUM(kitchen_usage_items.quantity) as total_qty'),
            ])
            ->join('kitchen_usages', 'kitchen_usages.id', '=', 'kitchen_usage_items.usage_id')
            ->join('inventory_items', 'inventory_items.id', '=', 'kitchen_usage_items.item_id')
            ->whereBetween('kitchen_usages.date', [$start, $end])
            ->groupBy('usage_date', 'inventory_items.id', 'inventory_items.name', 'inventory_items.unit')
            ->orderBy('usage_date', 'desc');

        if (!$user->hasRole('Super Admin')) {
            $query->where('kitchen_usages.school_id', $user->school_id);
        }

        $rows = $query->get();
        return view('admin.inventory.reports.daily', [
            'rows' => $rows,
            'start' => $start,
            'end' => $end,
        ]);
    }

    // Monthly usage (aggregate by month and item)
    public function monthlyUsage(Request $request)
    {
        $user = auth()->user();
        $year = (int)($request->get('year', now()->year));

        $query = KitchenUsageItem::query()
            ->select([
                DB::raw('DATE_FORMAT(kitchen_usages.date, "%Y-%m") as ym'),
                'inventory_items.id as item_id',
                'inventory_items.name as item_name',
                'inventory_items.unit as unit',
                DB::raw('SUM(kitchen_usage_items.quantity) as total_qty'),
            ])
            ->join('kitchen_usages', 'kitchen_usages.id', '=', 'kitchen_usage_items.usage_id')
            ->join('inventory_items', 'inventory_items.id', '=', 'kitchen_usage_items.item_id')
            ->whereYear('kitchen_usages.date', $year)
            ->groupBy('ym', 'inventory_items.id', 'inventory_items.name', 'inventory_items.unit')
            ->orderBy('ym', 'desc');

        if (!$user->hasRole('Super Admin')) {
            $query->where('kitchen_usages.school_id', $user->school_id);
        }

        $rows = $query->get();
        return view('admin.inventory.reports.monthly', [
            'rows' => $rows,
            'year' => $year,
        ]);
    }

    // Cost analysis based on Stock In (sum of quantity * cost_price per entry)
    public function costAnalysis(Request $request)
    {
        $user = auth()->user();
        $start = $request->date('start', now()->startOfMonth());
        $end = $request->date('end', now()->endOfMonth());

        $query = StockIn::query()
            ->select([
                'inventory_items.id as item_id',
                'inventory_items.name as item_name',
                DB::raw('SUM(stock_ins.quantity) as total_qty'),
                DB::raw('SUM(stock_ins.quantity * stock_ins.cost_price) as total_cost'),
            ])
            ->join('inventory_items', 'inventory_items.id', '=', 'stock_ins.item_id')
            ->whereBetween('stock_ins.date', [$start, $end])
            ->groupBy('inventory_items.id', 'inventory_items.name')
            ->orderBy('item_name');

        if (!$user->hasRole('Super Admin')) {
            $query->where('inventory_items.school_id', $user->school_id);
        }

        $rows = $query->get();
        $grandQty = $rows->sum('total_qty');
        $grandCost = $rows->sum('total_cost');

        return view('admin.inventory.reports.cost', [
            'rows' => $rows,
            'start' => $start,
            'end' => $end,
            'grandQty' => $grandQty,
            'grandCost' => $grandCost,
        ]);
    }
}
