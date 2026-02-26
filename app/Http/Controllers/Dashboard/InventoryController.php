<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\InventoryRequest;
use App\Models\InventoryItems;
use App\Models\InventoryLog;
use App\Services\InventoryService;
use App\Services\ForecastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected ForecastService  $forecastService
    ) {}

    public function index()
    {
        $lowStock        = $this->inventoryService->getLowStockItems();
        $totalItems      = InventoryItems::count();
        $lowStockCount   = $lowStock->count();
        $stockValue      = InventoryItems::selectRaw('SUM(current_stock * cost_per_unit) as total')->value('total') ?? 0;
        $todayUsageCount = InventoryLog::where('type', 'consumption')
            ->whereDate('created_at', today())
            ->count();

        return view('dashboard.inventory.index', compact(
            'lowStock', 'totalItems', 'lowStockCount', 'stockValue', 'todayUsageCount'
        ));
    }

    public function create()
    {
        return view('dashboard.inventory.create');
    }

    public function store(InventoryRequest $request)
    {
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
                InventoryLog::create([
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
            return view('dashboard.inventory.edit', compact('item'));
        } catch (Exception $e) {
            Log::error('Inventory edit error: ' . $e->getMessage());
            toast('Item not found.', 'error');
            return redirect()->route('inventory.index');
        }
    }

    public function update(InventoryRequest $request, $id)
    {
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