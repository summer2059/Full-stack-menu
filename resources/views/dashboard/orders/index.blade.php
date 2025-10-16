@extends('dashboard.layouts.app')

@push('css')
<style>
    /* ✅ Internal CSS for styling the table & status badges */
    .status-wrapper {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 4px;
        text-align: center;
    }
    .bg-warning { background-color: #ffc107 !important; color: #000 !important; }
    .bg-info { background-color: #0dcaf0 !important; color: #000 !important; }
    .bg-success { background-color: #198754 !important; color: #fff !important; }
    .bg-primary { background-color: #0d6efd !important; color: #fff !important; }
    .bg-danger { background-color: #dc3545 !important; color: #fff !important; }
    .bg-secondary { background-color: #6c757d !important; color: #fff !important; }

    #toast-container > div {
        opacity: 0.95;
        border-radius: 6px;
        font-size: 14px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="card-toolbar mb-4 d-flex justify-content-end"></div>

    <div class="card">
        <div class="card-header border-1 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <h4>Order Lists</h4>
                </div>
            </div>
        </div>

        <div class="table-responsive theme-scrollbar">
            <table id="example1" class="table yajra-datatable">
                <thead>
                    <tr class="text-start text-black-500 fw-bold fs-7 text-uppercase gs-0">
                        <th>No</th>
                        <th>Menu</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Table No.</th>
                        <th>Status</th>
                        <th class="min-w-70px">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript">
$(function() {
    let table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('order.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'menu', name: 'menu' },
            { data: 'quantity', name: 'quantity' },
            { data: 'total_price', name: 'total_price' },
            { data: 'table_number', name: 'table_number' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // ✅ Handle status change with live badge update
    $(document).on('change', '.order-status', function() {
        let orderId = $(this).data('id');
        let newStatus = $(this).val();
        let badge = $(this).closest('.status-wrapper').find('.status-badge');

        $.ajax({
            url: "{{ route('order.updateStatus') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: orderId,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    // ✅ update badge color dynamically
                    badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    badge.removeClass().addClass('status-badge bg-' + getStatusClass(newStatus));
                    toastr.success(response.message, 'Success');
                }
            },
            error: function() {
                toastr.error('Something went wrong while updating status!', 'Error');
            }
        });
    });

    // ✅ helper function to match badge color
    function getStatusClass(status) {
        switch (status) {
            case 'pending': return 'warning';
            case 'preparing': return 'info';
            case 'served': return 'success';
            case 'payed': return 'primary';
            case 'cancelled': return 'danger';
            default: return 'secondary';
        }
    }
});
</script>
@endpush
