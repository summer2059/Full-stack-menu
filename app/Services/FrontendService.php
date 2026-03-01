<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class FrontendService
{
    public function getMenuItems()
    {
        return Menu::with('menuCategory')->where('status', 1)->orderBy('priority')->latest()->get();
    }

    public function decryptTableToken(?string $token): ?string
    {
        if (!$token) {
            return null;
        }

        try {
            return Crypt::decryptString($token);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function generateUUID(): string
    {
        $uuid = (string) Str::uuid();
        Customer::create(['uuid' => $uuid]);

        return $uuid;
    }

    public function addToCart(string $uuid, int $menuId): Customer
    {
        $customer = Customer::firstOrCreate(
            ['uuid' => $uuid],
            ['name' => null, 'phone' => null]
        );

        $menu = Menu::findOrFail($menuId);

        $cartItem = Cart::where('customer_id', $customer->id)->where('menu_id', $menuId)->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
            $cartItem->update([
                'total_price' => $menu->price * $cartItem->quantity,
            ]);
        } else {
            Cart::create([
                'customer_id' => $customer->id,
                'menu_id'     => $menuId,
                'quantity'    => 1,
                'total_price' => $menu->price,
                'is_select'   => 1,
            ]);
        }

        return $customer;
    }

    public function getCart(string $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->first();

        if (!$customer) {
            return ['items' => [], 'total' => 0];
        }

        $items = Cart::where('customer_id', $customer->id)
            ->with('menu')
            ->get()
            ->map(function ($cart) {
                return [
                    'cart_id'     => $cart->id,
                    'menu_id'     => $cart->menu_id,
                    'name'        => $cart->menu?->title ?? 'Unknown',
                    'price'       => (float) ($cart->menu?->price ?? 0),
                    'quantity'    => (int) $cart->quantity,
                    'total_price' => (float) $cart->total_price,
                    'is_select'   => (int) $cart->is_select,
                ];
            })
            ->values()
            ->toArray();

        $total = collect($items)
            ->where('is_select', 1)
            ->sum('total_price');

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    public function updateCart(int $cartId, int $quantity): void
    {
        $cart = Cart::findOrFail($cartId);

        if ($quantity === 0) {
            $cart->delete();
            return;
        }

        $menu = Menu::findOrFail($cart->menu_id);

        $cart->update([
            'quantity'    => $quantity,
            'total_price' => $menu->price * $quantity,
        ]);
    }

    public function toggleCartSelect(int $cartId, bool $isSelect): void
    {
        Cart::where('id', $cartId)->update(['is_select' => $isSelect]);
    }

    public function removeFromCart(int $cartId): void
    {
        Cart::destroy($cartId);
    }

    public function submitOrder(array $data): void
    {
        $customer = Customer::where('uuid', $data['uuid'])->firstOrFail();
        $customer->update([
            'name'  => $data['user_name'],
            'phone' => $data['user_phone'] ?? null,
        ]);
        $selectedItems = Cart::where('customer_id', $customer->id)->where('is_select', 1)->with('menu')->get();

        if ($selectedItems->isEmpty()) {
            throw new \Exception('No items selected for order.');
        }
        foreach ($selectedItems as $item) {
            Order::create([
                'customer_id'  => $customer->id,
                'menu_id'      => $item->menu_id,
                'quantity'     => $item->quantity,
                'total_price'  => $item->total_price,
                'table_number' => $data['table'],
                'note'         => $data['note'] ?? null,
                'status'       => 'pending',
            ]);
        }
        Cart::where('customer_id', $customer->id)->where('is_select', 1)->delete();
    }
}