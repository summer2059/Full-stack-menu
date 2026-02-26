<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class FrontendController extends Controller
{
    /**
     * Show menu — table number decoded from encrypted token in URL
     * QR URL format: /menu/{token}
     * e.g. /menu/eyJpdiI6Ik1...  (looks random, hides table number)
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
                // Invalid or tampered token — show menu without table
                $tableNumber = null;
            }
        }
        return view('frontend.index', compact('menuItems', 'tableNumber'));
    }

    /**
     * Generate encrypted QR URL for a table.
     * Call this from your admin QR generator.
     * Usage: FrontendController::generateTableUrl(5)
     */
    public static function generateTableUrl(int $tableNumber): string
    {
        $token = Crypt::encryptString((string) $tableNumber);
        return route('menu.index', ['token' => $token]);
    }

    public function submit(Request $request)
    {
        try {
            $data = $request->validate([
                'table'        => 'required|integer|min:1',
                'phone'        => 'nullable|string',
                'menu_ids'     => 'required|array',
                'menu_ids.*'   => 'integer|exists:menus,id',
                'quantities'   => 'required|array',
                'quantities.*' => 'integer|min:1',
            ]);

            foreach ($data['menu_ids'] as $index => $menuId) {
                $quantity = $data['quantities'][$index];
                $menu     = Menu::find($menuId);

                if (!$menu) {
                    throw new \Exception("Menu item not found: ID {$menuId}");
                }

                Order::create([
                    'menu_id'      => $menuId,
                    'quantity'     => $quantity,
                    'total_price'  => $menu->price * $quantity,
                    'table_number' => $data['table'],
                    'status'       => 'pending',
                ]);
            }

            toast('Order submitted successfully!', 'success');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::error('Order submission error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}