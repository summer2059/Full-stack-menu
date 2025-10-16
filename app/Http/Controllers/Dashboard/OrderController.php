<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\CrudService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Order;

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
            $data = Order::with('menu')->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('menu', function ($data) {
                    return $data->menu ? $data->menu->title : 'N/A';
                })
                ->addColumn('status', function ($data) {
                    $statuses = ['pending', 'preparing', 'served', 'payed', 'cancelled'];
                    $options = '';
                    foreach ($statuses as $status) {
                        $selected = $data->status == $status ? 'selected' : '';
                        $options .= "<option value='{$status}' {$selected}>".ucfirst($status)."</option>";
                    }

                    $badgeClass = match($data->status) {
                        'pending' => 'badge bg-warning text-dark',
                        'preparing' => 'badge bg-info text-dark',
                        'served' => 'badge bg-success',
                        'payed' => 'badge bg-primary',
                        'cancelled' => 'badge bg-danger',
                        default => 'badge bg-secondary',
                    };

                    return "
                        <div class='status-wrapper'>
                            <span class='{$badgeClass} status-badge'>".ucfirst($data->status)."</span>
                            <select class='form-select order-status' data-id='{$data->id}'>{$options}</select>
                        </div>
                    ";
                })
                ->addColumn('action', function ($data) {
                    return '<a href="order/' . $data->id . '" class="btn btn-sm btn-danger" data-confirm-delete="true">Delete</a>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('dashboard.orders.index');
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
            'id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,preparing,served,payed,cancelled'
        ]);

        $order = Order::findOrFail($request->id);
        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Order status updated successfully!']);
    }
}
