<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    /**
     * Show menu — table number decoded from encrypted token in URL
     */
    public function index($token = null)
    {
        $menuItems = Menu::with('menuCategory')
            ->where('status', 1)
            ->orderBy('priority')
            ->latest()
            ->get();

        $tableNumber = null;

        if ($token) {
            try {
                $tableNumber = Crypt::decryptString($token);
            } catch (\Exception $e) {
                $tableNumber = null;
            }
        }

        return view('frontend.index', compact('menuItems', 'tableNumber'));
    }

    /**
     * Generate encrypted QR URL for a table.
     */
    public static function generateTableUrl(int $tableNumber): string
    {
        $token = Crypt::encryptString((string) $tableNumber);
        return route('menu.index', ['token' => $token]);
    }

    /**
     * Generate a UUID from DB and return it — used by frontend on session start
     */
    public function generateUUID()
    {
        $uuid     = (string) Str::uuid();
        $customer = Customer::create(['uuid' => $uuid]);

        return response()->json(['uuid' => $customer->uuid]);
    }

    
    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'uuid'    => 'required|string',
                'menu_id' => 'required|integer|exists:menus,id',
            ]);

            $uuid   = $request->uuid;
            $menuId = $request->menu_id;

            // Find or create customer by UUID
            $customer = Customer::firstOrCreate(
                ['uuid' => $uuid],
                ['name' => null, 'phone' => null]
            );

            $menu = Menu::findOrFail($menuId);

            // Check if item already in cart for this customer
            $cartItem = Cart::where('customer_id', $customer->id)
                ->where('menu_id', $menuId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity   += 1;
                $cartItem->total_price = $menu->price * $cartItem->quantity;
                $cartItem->save();
            } else {
                Cart::create([
                    'customer_id' => $customer->id,
                    'menu_id'     => $menuId,
                    'quantity'    => 1,
                    'total_price' => $menu->price,
                    'is_select'   => 1,
                ]);
            }

            return response()->json([
                'success'     => true,
                'customer_id' => $customer->id,
                'uuid'        => $customer->uuid,
            ]);

        } catch (\Exception $e) {
            Log::error('Add to cart error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get cart items for a customer UUID
     */
    public function getCart(Request $request)
    {
        $uuid = $request->query('uuid');

        if (!$uuid) {
            return response()->json(['items' => [], 'total' => 0]);
        }

        $customer = Customer::where('uuid', $uuid)->first();

        if (!$customer) {
            return response()->json(['items' => [], 'total' => 0]);
        }

        $items = Cart::where('customer_id', $customer->id)
            ->with('menu')
            ->get()
            ->map(function ($cart) {
                return [
                    'cart_id'     => $cart->id,
                    'menu_id'     => $cart->menu_id,
                    'name'        => $cart->menu?->title ?? 'Unknown',
                    'price'       => $cart->menu?->price ?? 0,
                    'quantity'    => $cart->quantity,
                    'total_price' => $cart->total_price,
                    'is_select'   => $cart->is_select,
                ];
            });

        $total = $items->where('is_select', 1)->sum('total_price');

        return response()->json(['items' => $items, 'total' => $total]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request)
    {
        try {
            $request->validate([
                'cart_id'  => 'required|integer|exists:carts,id',
                'quantity' => 'required|integer|min:0',
            ]);

            $cart = Cart::findOrFail($request->cart_id);
            $menu = Menu::findOrFail($cart->menu_id);

            if ($request->quantity === 0) {
                $cart->delete();
            } else {
                $cart->quantity    = $request->quantity;
                $cart->total_price = $menu->price * $request->quantity;
                $cart->save();
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Toggle is_select on a cart item
     */
    public function toggleCartSelect(Request $request)
    {
        try {
            $request->validate([
                'cart_id'   => 'required|integer|exists:carts,id',
                'is_select' => 'required|boolean',
            ]);

            Cart::where('id', $request->cart_id)->update(['is_select' => $request->is_select]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove a single cart item
     */
    public function removeFromCart(Request $request)
    {
        try {
            $request->validate(['cart_id' => 'required|integer|exists:carts,id']);
            Cart::destroy($request->cart_id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Submit order — only is_select=1 items, then remove them from cart
     */
    public function submit(Request $request)
    {
        try {
            $data = $request->validate([
                'uuid'       => 'required|string',
                'user_name'  => 'required|string|max:255',
                'table'      => 'required|integer|min:1',
                'user_phone' => 'nullable|string',
                'note'       => 'nullable|string',
            ]);

            // Find customer
            $customer = Customer::where('uuid', $data['uuid'])->first();
            if (!$customer) {
                throw new \Exception('Customer session not found. Please add items to cart again.');
            }

            // Update customer name & phone
            $customer->update([
                'name'  => $data['user_name'],
                'phone' => $data['user_phone'] ?? null,
            ]);

            // Get only selected cart items
            $selectedItems = Cart::where('customer_id', $customer->id)
                ->where('is_select', 1)
                ->with('menu')
                ->get();

            if ($selectedItems->isEmpty()) {
                throw new \Exception('No items selected for order.');
            }

            foreach ($selectedItems as $cartItem) {
                if (!$cartItem->menu) continue;

                Order::create([
                    'customer_id'  => $customer->id,
                    'menu_id'      => $cartItem->menu_id,
                    'quantity'     => $cartItem->quantity,
                    'total_price'  => $cartItem->total_price,
                    'table_number' => $data['table'],
                    'note'         => $data['note'] ?? null,
                    'status'       => 'pending',
                ]);
            }

            // Remove ordered items from cart
            Cart::where('customer_id', $customer->id)
                ->where('is_select', 1)
                ->delete();

            toast('Order submitted successfully!', 'success');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('Order submission error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}