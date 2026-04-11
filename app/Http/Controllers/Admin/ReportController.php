<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function inventory(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $itemId = $request->input('item_id');
        $movementType = $request->input('movement_type', 'all');

        // Get all inventory items for filter dropdown
        $items = InventoryItem::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get();

        // Query stock movements
        $query = StockIn::with('item')
            ->whereHas('item', function ($q) {
                $q->where('school_id', auth()->user()->school_id);
            })
            ->whereBetween('date', [$startDate, $endDate]);

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        $stockIns = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'date' => $item->date,
                'time' => $item->created_at->format('H:i:s'),
                'item_name' => $item->item->name,
                'movement_type' => 'Stock In',
                'quantity' => $item->quantity,
                'unit' => $item->item->unit,
                'reference' => $item->supplier,
                'created_at' => $item->created_at,
            ];
        });

        // Query stock outs
        $query = StockOut::with('item')
            ->whereHas('item', function ($q) {
                $q->where('school_id', auth()->user()->school_id);
            })
            ->whereBetween('date', [$startDate, $endDate]);

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        $stockOuts = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'date' => $item->date,
                'time' => $item->created_at->format('H:i:s'),
                'item_name' => $item->item->name,
                'movement_type' => 'Stock Out',
                'quantity' => $item->quantity,
                'unit' => $item->item->unit,
                'reference' => $item->reason,
                'created_at' => $item->created_at,
            ];
        });

        // Merge and sort movements
        $allMovements = $stockIns->concat($stockOuts)
            ->when($movementType !== 'all', function ($collection) use ($movementType) {
                return $collection->filter(function ($item) use ($movementType) {
                    return $movementType === 'in' 
                        ? $item['movement_type'] === 'Stock In'
                        : $item['movement_type'] === 'Stock Out';
                });
            })
            ->sortByDesc('created_at')
            ->values();

        // Calculate summary statistics
        $summary = [
            'total_in' => $stockIns->sum('quantity'),
            'total_out' => $stockOuts->sum('quantity'),
            'unique_items' => $allMovements->pluck('item_name')->unique()->count(),
            'total_movements' => $allMovements->count(),
        ];

        return view('admin.reports.inventory', compact(
            'allMovements',
            'items',
            'summary',
            'startDate',
            'endDate',
            'itemId',
            'movementType'
        ));
    }

    public function view()
    {
        return view('admin.reports.view');
    }

    public function export()
    {
        return response()->download('');
    }

    public function download(Request $request)
    {
        return response()->download('');
    }
}
