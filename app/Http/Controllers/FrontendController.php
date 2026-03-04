<?php

namespace App\Http\Controllers;

use App\Http\Requests\Frontend\AddToCartequest;
use App\Http\Requests\Frontend\RemoveFromCartRequest;
use App\Http\Requests\Frontend\SubmitOrderRequest;
use App\Http\Requests\Frontend\ToggleCartSelectRequest;
use App\Http\Requests\Frontend\UpdateCartRequest;
use App\Services\FrontendService;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function __construct(
        protected FrontendService $service
    ) {}

    public function index(?string $token = null)
    {
        $menuItems   = $this->service->getMenuItems();
        $tableNumber = $this->service->decryptTableToken($token);

        return view('frontend.index', compact('menuItems', 'tableNumber'));
    }

    public function generateUUID()
    {
        return response()->json([
            'uuid' => $this->service->generateUUID()
        ]);
    }

    public function addToCart(AddToCartequest $request)
    {
        $customer = $this->service->addToCart(
            $request->uuid,
            $request->menu_id
        );

        return response()->json([
            'success'     => true,
            'customer_id' => $customer->id,
            'uuid'        => $customer->uuid,
        ]);
    }

    public function getCart(Request $request)
    {
        $data = $this->service->getCart($request->query('uuid'));

        return response()->json($data);
    }

    public function updateCart(UpdateCartRequest $request)
    {
        $this->service->updateCart(
            $request->cart_id,
            $request->quantity
        );

        return response()->json(['success' => true]);
    }

    public function toggleCartSelect(ToggleCartSelectRequest $request)
    {
        $this->service->toggleCartSelect(
            $request->cart_id,
            $request->is_select
        );

        return response()->json(['success' => true]);
    }

    public function removeFromCart(RemoveFromCartRequest $request)
    {
        $this->service->removeFromCart($request->cart_id);

        return response()->json(['success' => true]);
    }

    public function submit(SubmitOrderRequest $request)
    {
        $this->service->submitOrder($request->validated());

        toast('Order submitted successfully!', 'success');

        return redirect()->back();
    }

    /**
     * Track orders by customer UUID.
     */
    public function trackOrders(Request $request)
    {
        $uuid   = $request->query('uuid');
        $orders = $this->service->getOrdersByUUID($uuid);

        return response()->json(['orders' => $orders]);
    }

    /**
     * Cancel a pending order with optional remark.
     */
    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'remark'   => 'nullable|string|max:500',
        ]);

        $result = $this->service->cancelOrder(
            $request->order_id,
            $request->remark
        );

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        return response()->json(['success' => true]);
    }
}