@extends('dashboard.layouts.app')

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
@endpush

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="mb-0">🧾 Bill for Table #{{ $table_number }}</h4>
            <a href="{{ route('order.index') }}" class="btn btn-secondary">⬅ Back to Orders</a>
        </div>
        <div class="card-body">
            @if($orders->isEmpty())
                <div class="alert alert-success">✅ All orders for this table are paid.</div>
            @else
                <form id="markAllPaidForm" action="{{ route('order.markAllPaid') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="table_number" value="{{ $table_number }}">
                    <button type="button" id="markAllPaidBtn" class="btn btn-success">
                        💰 Mark All as Paid
                    </button>
                </form>

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>SN</th>
                            <th>Menu</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($orders as $key => $order)
                            @php $grandTotal += $order->total_price; @endphp
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $order->menu->title ?? 'N/A' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>Rs.{{ number_format($order->total_price / $order->quantity, 2) }}</td>
                                <td>Rs.{{ number_format($order->total_price, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ match($order->status) {
                                        'pending' => 'warning',
                                        'preparing' => 'info',
                                        'served' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary',
                                    } }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Grand Total</th>
                            <th colspan="2">Rs.{{ number_format($grandTotal, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('markAllPaidBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Confirm Payment Received',
        text: "Are you sure you have received the money for all these orders?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark as paid!',
        cancelButtonText: 'No, cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form to mark all orders paid
            document.getElementById('markAllPaidForm').submit();
        }
    });
});
</script>
@endpush
