@extends('dashboard.layouts.app')

@section('content')
<div id="kt_app_content_container" class="app-container  container-xxl ">
    <div class="card">
        <div class="card-header border-1 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <h4>Copleted Orders</h4>
                </div>
            </div>
        </div>
        <div class="table-responsive theme-scrollbar">
        <table id="example1" class="table yajra-datatable">
            <thead>
                <tr class="text-start text-black-500 fw-bold fs-7 text-uppercase gs-0">
                        <th>SN</th>
                        <th>Menu</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Table No.</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script type="text/javascript">
$(function () {
    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('order.completed') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'menu', name: 'menu' },
            { data: 'quantity', name: 'quantity' },
            { data: 'total_price', name: 'total_price' },
            { data: 'table_number', name: 'table_number' },
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
