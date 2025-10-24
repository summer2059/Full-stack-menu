@extends('dashboard.layouts.app')

@push('css')
<style>
    .table th,
    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!-- Toolbar -->
        <div class="card-toolbar mb-4">
            <div class="d-flex justify-content-end">
                <a href="{{ route('order.index') }}" class="btn btn-sm btn-secondary">
                    ⬅ Back to Orders
                </a>
            </div>
        </div>

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header border-1 pt-6">
                <div class="card-title">
                    <h4>🧾 Bill for Table #{{ $table_number }}</h4>
                </div>
            </div>

            <!-- Body -->
            <div class="card-body">
                @if($orders->isEmpty())
                    <div class="alert alert-success">✅ All orders for this table are paid.</div>
                @else
                    <form id="markAllPaidForm" action="{{ route('order.markAllPaid') }}" method="POST" class="mb-4">
                        @csrf
                        <input type="hidden" name="table_number" value="{{ $table_number }}">
                        <button type="button" id="markAllPaidBtn" class="btn btn-success">
                            💰 Mark All as Paid
                        </button>
                    </form>

                    <div class="table-responsive theme-scrollbar">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light text-uppercase text-muted fs-7">
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
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end">Grand Total</td>
                                    <td colspan="2">Rs.{{ number_format($grandTotal, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
            document.getElementById('markAllPaidForm').submit();
        }
    });
});
</script>
@endpush
