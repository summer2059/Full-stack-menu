<?php
// app/Services/InventoryService.php

namespace App\Services;

use App\Models\InventoryItems;
use App\Models\InventoryLog;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function deductForOrder(Order $order): void
    {
        
        $alreadyDeducted = InventoryLog::where('order_id', $order->id)
            ->where('type', 'consumption')
            ->exists();

        if ($alreadyDeducted) return;

        $menu = $order->menu()->with('inventoryItems')->first();
        if (!$menu) return;

        DB::transaction(function () use ($menu, $order) {
            foreach ($menu->inventoryItems as $item) {
                $required = $item->pivot->quantity_required * $order->quantity;

                $item->decrement('current_stock', $required);
                $item->refresh();

                InventoryLog::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'consumption',
                    'quantity'          => -$required,
                    'stock_after'       => $item->current_stock,
                    'order_id'          => $order->id,
                    'note'              => "Order #{$order->id} — {$menu->title} x{$order->quantity}",
                ]);
            }
        });
    }

    public function restoreForOrder(Order $order): void
    {
        $logs = InventoryLog::where('order_id', $order->id)
            ->where('type', 'consumption')
            ->get();

        if ($logs->isEmpty()) return;

        DB::transaction(function () use ($logs, $order) {
            foreach ($logs as $log) {
                $item = InventoryItems::find($log->inventory_item_id);
                if (!$item) continue;

                $restoreQty = abs($log->quantity);
                $item->increment('current_stock', $restoreQty);
                $item->refresh();

                InventoryLog::create([
                    'inventory_item_id' => $item->id,
                    'type'              => 'adjustment',
                    'quantity'          => $restoreQty,
                    'stock_after'       => $item->current_stock,
                    'order_id'          => $order->id,
                    'note'              => "Restored — Order #{$order->id} cancelled",
                ]);
            }
        });
    }

    public function restock(int $itemId, float $quantity, string $note = ''): InventoryItems
    {
        $item = InventoryItems::findOrFail($itemId);

        DB::transaction(function () use ($item, $quantity, $note) {
            $item->increment('current_stock', $quantity);
            $item->refresh();

            InventoryLog::create([
                'inventory_item_id' => $item->id,
                'type'              => 'restock',
                'quantity'          => $quantity,
                'stock_after'       => $item->current_stock,
                'note'              => $note ?: 'Manual restock',
            ]);
        });

        return $item->fresh();
    }

    public function getLowStockItems()
    {
        return InventoryItems::whereRaw('current_stock <= minimum_stock')->get();
    }

    public function todayConsumption()
    {
        return InventoryLog::with('inventoryItem')
            ->where('type', 'consumption')
            ->whereDate('created_at', today())
            ->get()
            ->groupBy('inventory_item_id')
            ->map(fn($logs) => [
                'item'  => $logs->first()->inventoryItem->name ?? '—',
                'total' => abs($logs->sum('quantity')),
                'unit'  => $logs->first()->inventoryItem->unit ?? '',
            ]);
    }
}