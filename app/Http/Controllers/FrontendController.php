<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontendController extends Controller
{
    public function index()
    {
         $menuItems = Menu::with('menuCategory')->where('status', 1)->orderBy('priority')->latest()->get();

        return view('frontend.index', compact('menuItems'));
    }

    public function submit(Request $request)
    {
        try {
            $data = $request->validate([
                // 'name' => 'required|string|max:255',
                'table' => 'required|integer|min:1',
                'phone' => 'nullable|string',
                // 'notes' => 'nullable|string',
                'menu_ids' => 'required|array',
                'menu_ids.*' => 'integer|exists:menus,id',
                'quantities' => 'required|array',
                'quantities.*' => 'integer|min:1',
            ]);

            foreach ($data['menu_ids'] as $index => $menuId) {
                $quantity = $data['quantities'][$index];

                $menu = Menu::find($menuId);
                if (!$menu) {
                    throw new \Exception("Menu item not found: ID {$menuId}");
                }

                $totalPrice = $menu->price * $quantity;

                Order::create([
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                    'table_number' => $data['table'],
                    'status' => 'pending',
                ]);
            }

            toast('Order submitted successfully!', 'success');
            return redirect()->back();

        } catch (\Exception $e) {
            // Log error for debugging if needed
            Log::error('Order submission error: ' . $e->getMessage());

            // SweetAlert error via session
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
