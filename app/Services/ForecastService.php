<?php

namespace App\Services;


use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ForecastService
{
    /**
     * Forecast tomorrow's sales based on past N days average for same day-of-week.
     */
    public function forecastTomorrow(int $weeksBack = 4): array
    {
        $tomorrow = Carbon::tomorrow();
        $dayOfWeek = $tomorrow->dayOfWeek;

        // Collect same day-of-week sales for past N weeks
        $historicalOrders = collect();
        for ($i = 1; $i <= $weeksBack; $i++) {
            $date = Carbon::tomorrow()->subWeeks($i);
            $dailyOrders = Order::with('menu')
                ->whereDate('created_at', $date)
                ->where('status', 'payed')
                ->get();
            $historicalOrders->push($dailyOrders);
        }

        // Average per menu item
        $menuTotals = [];
        foreach ($historicalOrders as $dayOrders) {
            foreach ($dayOrders as $order) {
                $key = $order->menu_id;
                $menuTotals[$key]['title']    = $order->menu->title ?? 'Unknown';
                $menuTotals[$key]['quantities'][] = $order->quantity;
            }
        }

        $forecast = [];
        foreach ($menuTotals as $menuId => $data) {
            $avgQty = array_sum($data['quantities']) / $weeksBack;
            $forecast[] = [
                'menu_id'            => $menuId,
                'menu_title'         => $data['title'],
                'forecasted_qty'     => round($avgQty),
                'confidence'         => count($data['quantities']) >= 3 ? 'high' : 'low',
            ];
        }

        // Sort by forecasted_qty descending
        usort($forecast, fn($a, $b) => $b['forecasted_qty'] - $a['forecasted_qty']);

        return [
            'date'     => $tomorrow->format('Y-m-d'),
            'day_name' => $tomorrow->format('l'),
            'items'    => $forecast,
        ];
    }

    /**
     * Get today's actual vs yesterday's sales comparison.
     */
    public function todayVsYesterday(): array
    {
        $today = Order::where('status', 'payed')
            ->whereDate('created_at', today())
            ->sum('total_price');

        $yesterday = Order::where('status', 'payed')
            ->whereDate('created_at', Carbon::yesterday())
            ->sum('total_price');

        $change = $yesterday > 0
            ? round((($today - $yesterday) / $yesterday) * 100, 1)
            : 0;

        return [
            'today'     => $today,
            'yesterday' => $yesterday,
            'change'    => $change,
            'trend'     => $change >= 0 ? 'up' : 'down',
        ];
    }

    /**
     * Get recommended inventory quantities for tomorrow based on forecast.
     */
    public function inventoryNeededForTomorrow(): array
    {
        $forecast = $this->forecastTomorrow();
        $needed = [];

        foreach ($forecast['items'] as $item) {
            $menu = \App\Models\Menu::with('inventoryItems')->find($item['menu_id']);
            if (!$menu) continue;

            foreach ($menu->inventoryItems as $invItem) {
                $key = $invItem->id;
                $required = $invItem->pivot->quantity_required * $item['forecasted_qty'];

                if (!isset($needed[$key])) {
                    $needed[$key] = [
                        'item'          => $invItem->name,
                        'unit'          => $invItem->unit,
                        'current_stock' => $invItem->current_stock,
                        'needed'        => 0,
                    ];
                }
                $needed[$key]['needed'] += $required;
            }
        }

        // Calculate shortfall
        foreach ($needed as &$row) {
            $row['shortfall'] = max(0, $row['needed'] - $row['current_stock']);
            $row['sufficient'] = $row['shortfall'] == 0;
        }

        return array_values($needed);
    }
}