<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CrudService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    protected $crudService;
    protected $modelName;

    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
        $this->modelName = 'order';
    }

    public function index(Request $request)
    {
        $title = 'Delete Order!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        if ($request->ajax()) {
            $data = Order::select('table_number')
                ->where('status', '!=', 'payed')
                ->groupBy('table_number')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    return '<a href="' . route('order.byTable', $data->table_number) . '" class="btn btn-sm btn-primary">View</a>';
                })
                ->make(true);
        }

        return view('dashboard.orders.index');
    }

    public function showUnpaidOrdersByTable($table_number)
    {
        $orders = Order::with('menu')
            ->where('table_number', $table_number)
            ->where('status', '!=', 'payed')
            ->get();

        return view('dashboard.orders.by_table', compact('orders', 'table_number'));
    }

    public function markAllPaid(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer'
        ]);
        $inventoryService = app(InventoryService::class);
        $orders = Order::where('table_number', $request->table_number)
            ->where('status', '!=', 'payed')->get();

        foreach ($orders as $order) {
            $previousStatus = $order->status;
            $order->status = 'payed';
            $order->save();
            if ($previousStatus !== 'payed') {
                $inventoryService->deductForOrder($order);
            }
        }

        toast('All orders marked as paid!', 'success');
        return redirect()->route('order.index');
    }

    public function destroy($id)
    {
        $this->crudService->delete($this->modelName, $id);
        toast('Order Deleted!', 'success');
        return redirect()->route('order.index');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id'     => 'required|exists:orders,id',
            'status' => 'required|in:pending,preparing,served,payed,cancelled'
        ]);

        $order          = Order::findOrFail($request->id);
        $previousStatus = $order->status;

        $order->status = $request->status;
        $order->save();

        $inventoryService = app(InventoryService::class);

        // Deduct when order is PAID
        if ($request->status === 'payed' && $previousStatus !== 'payed') {
            $inventoryService->deductForOrder($order);
        }

        // Restore stock if cancelled (only if it was already paid)
        if ($request->status === 'cancelled' && $previousStatus === 'payed') {
            $inventoryService->restoreForOrder($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated!'
        ]);
    }
    public function completedOrders(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('menu')
                ->where('status', 'payed')
                ->latest()
                ->get();

            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('menu', fn($data) => $data->menu->title ?? 'N/A')
                ->addColumn('status', function ($data) {
                    return '<span class="badge bg-primary">Payed</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('dashboard.orders.completed');
    }
}
