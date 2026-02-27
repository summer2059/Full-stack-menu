<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\InventoryItems;
use App\Services\InventoryService;
use App\Services\ForecastService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected ForecastService  $forecastService
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = InventoryItems::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('stock_status', function ($item) {
                    if ($item->current_stock <= 0) {
                        return '<span class="stock-badge stock-low">Out of Stock</span>';
                    } elseif ($item->isLowStock()) {
                        return '<span class="stock-badge stock-warning">Low Stock</span>';
                    }
                    return '<span class="stock-badge stock-ok">✓ OK</span>';
                })
                ->addColumn('action', function ($item) {
                    $buttons = '';

                    // EDIT — inventory_manager + admin
                    if (auth()->user()->can('inventory.update')) {
                        $buttons .= '<a href="' . route('inventory.edit', $item->id) . '"
                            class="btn btn-sm btn-primary me-1">
                            <i class="fa fa-pencil me-1"></i>Edit
                        </a>';
                    }

                    // RESTOCK — inventory_manager + admin
                    if (auth()->user()->can('inventory.restock')) {
                        $buttons .= '<button class="btn btn-sm btn-success me-1 restock-btn"
                            data-id="'   . $item->id   . '"
                            data-name="' . e($item->name) . '"
                            data-unit="' . $item->unit . '">
                            <i class="fa fa-plus me-1"></i>Restock
                        </button>';
                    }

                    // DELETE — admin only
                    if (auth()->user()->can('inventory.delete')) {
                        $buttons .= '<a href="' . route('inventory.destroy', $item->id) . '"
                            class="btn btn-sm btn-danger"
                            data-confirm-delete="true">
                            <i class="fa fa-trash me-1"></i>Delete
                        </a>';
                    }

                    return $buttons ?: '<span class="text-muted small">View only</span>';
                })
                ->rawColumns(['stock_status', 'action'])
                ->make(true);
        }

        $lowStock        = $this->inventoryService->getLowStockItems();
        $totalItems      = InventoryItems::count();
        $lowStockCount   = $lowStock->count();
        $stockValue      = InventoryItems::selectRaw('SUM(current_stock * cost_per_unit) as total')->value('total') ?? 0;
        $todayUsageCount = \App\Models\InventoryLog::where('type', 'consumption')
                            ->whereDate('created_at', today())
                            ->count();

        return view('dashboard.inventory.index', compact(
            'lowStock', 'totalItems', 'lowStockCount', 'stockValue', 'todayUsageCount'
        ));
    }

    public function create()
    {
        return view('dashboard.inventory.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'status'        => 'nullable|in:0,1',
        ]);

        try {
            $item = InventoryItems::create([
                'name'          => $request->name,
                'unit'          => $request->unit,
                'current_stock' => $request->current_stock,
                'minimum_stock' => $request->minimum_stock ?? 0,
                'cost_per_unit' => $request->cost_per_unit ?? 0,
                'status'        => $request->status ?? 1,
            ]);

            if ($item->current_stock > 0) {
                \App\Models\InventoryLog::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'restock',
                    'quantity'          => $item->current_stock,
                    'stock_after'       => $item->current_stock,
                    'note'              => 'Opening stock',
                ]);
            }

            toast('Inventory item added successfully!', 'success');
            return redirect()->route('inventory.index');
        } catch (Exception $e) {
            Log::error('Inventory store error: ' . $e->getMessage());
            toast('Failed to add inventory item.', 'error');
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $item = InventoryItems::findOrFail($id);
            return view('dashboard.inventory.form', compact('item'));
        } catch (Exception $e) {
            Log::error('Inventory edit error: ' . $e->getMessage());
            toast('Item not found.', 'error');
            return redirect()->route('inventory.index');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'status'        => 'nullable|in:0,1',
        ]);

        try {
            $item = InventoryItems::findOrFail($id);
            $item->update([
                'name'          => $request->name,
                'unit'          => $request->unit,
                'current_stock' => $request->current_stock,
                'minimum_stock' => $request->minimum_stock ?? 0,
                'cost_per_unit' => $request->cost_per_unit ?? 0,
                'status'        => $request->status ?? 1,
            ]);

            toast('Inventory item updated successfully!', 'success');
            return redirect()->route('inventory.index');
        } catch (Exception $e) {
            Log::error('Inventory update error: ' . $e->getMessage());
            toast('Failed to update inventory item.', 'error');
            return back()->withInput();
        }
    }

    public function restock(Request $request)
    {
        $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity'          => 'required|numeric|min:0.01',
            'note'              => 'nullable|string',
        ]);

        try {
            $this->inventoryService->restock(
                $request->inventory_item_id,
                $request->quantity,
                $request->note
            );
            toast('Stock restocked successfully!', 'success');
        } catch (Exception $e) {
            Log::error('Restock error: ' . $e->getMessage());
            toast('Failed to restock.', 'error');
        }

        return back();
    }

    public function destroy($id)
    {
        try {
            InventoryItems::findOrFail($id)->delete();
            toast('Inventory item deleted!', 'success');
        } catch (Exception $e) {
            Log::error('Inventory delete error: ' . $e->getMessage());
            toast('Failed to delete item.', 'error');
        }
        return redirect()->route('inventory.index');
    }

    public function forecast()
    {
        $forecast   = $this->forecastService->forecastTomorrow();
        $needed     = $this->forecastService->inventoryNeededForTomorrow();
        $comparison = $this->forecastService->todayVsYesterday();

        return view('dashboard.inventory.forecast', compact('forecast', 'needed', 'comparison'));
    }
}