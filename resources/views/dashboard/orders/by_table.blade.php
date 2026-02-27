@extends('dashboard.layouts.app')

@push('css')
<style>
    .table th, .table td { vertical-align: middle; }
</style>
@endpush

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">

    <div class="card-toolbar mb-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('order.index') }}" class="btn btn-sm btn-secondary">
                ⬅ Back to Orders
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-1 pt-6">
            <div class="card-title">
                <h4>🧾 Bill for Table #{{ $table_number }}</h4>
            </div>
        </div>

        <div class="card-body">
            @if($orders->isEmpty())
                <div class="alert alert-success">✅ All orders for this table are paid.</div>
            @else

                {{-- MARK ALL PAID — manager + reception + admin --}}
                @can('order.mark_paid')
                <form id="markAllPaidForm" action="{{ route('order.markAllPaid') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="table_number" value="{{ $table_number }}">
                    <button type="button" id="markAllPaidBtn" class="btn btn-success">
                        💰 Mark All as Paid
                    </button>
                </form>
                @endcan

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

                                {{-- STATUS UPDATE COLUMN — kitchen + food_server + manager + admin --}}
                                @can('order.update_status')
                                <th>Update Status</th>
                                @endcan
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
                                            'pending'   => 'warning',
                                            'preparing' => 'info',
                                            'served'    => 'success',
                                            'cancelled' => 'danger',
                                            default     => 'secondary',
                                        } }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>

                                    {{-- UPDATE STATUS DROPDOWN — kitchen + food_server + manager + admin --}}
                                    @can('order.update_status')
                                    <td>
                                        <select class="form-select form-select-sm order-status-select"
                                                data-id="{{ $order->id }}"
                                                style="min-width: 130px;">
                                            @foreach(['pending','preparing','served','cancelled'] as $s)
                                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                                    {{ ucfirst($s) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Grand Total</td>
                                <td colspan="{{ auth()->user()->can('order.update_status') ? 3 : 2 }}">
                                    Rs.{{ number_format($grandTotal, 2) }}
                                </td>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Mark All Paid confirmation
@can('order.mark_paid')
const markAllBtn = document.getElementById('markAllPaidBtn');
if (markAllBtn) {
    markAllBtn.addEventListener('click', function () {
        Swal.fire({
            title: 'Confirm Payment',
            text: 'Are you sure you have received the payment for all orders?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, mark as paid!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('markAllPaidForm').submit();
            }
        });
    });
}
@endcan

// Live status update per order — kitchen + food_server + manager + admin
@can('order.update_status')
$(document).on('change', '.order-status-select', function () {
    const id     = $(this).data('id');
    const status = $(this).val();
    const select = $(this);

    $.ajax({
        url: "{{ route('order.updateStatus') }}",
        method: 'POST',
        data: { _token: '{{ csrf_token() }}', id, status },
        success: function (res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Updated', text: res.message, timer: 1200, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
            }
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
        }
    });
});
@endcan
</script>
@endpush