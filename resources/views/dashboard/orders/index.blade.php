@extends('dashboard.layouts.app')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
@endpush

@section('content')
<div id="kt_app_content_container" class="app-container  container-xxl ">
    <div class="card">
        <div class="card-header border-1 pt-6">
                <div class="card-title">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1">
                        <h4>🧾 Tables with Unpaid Orders</h4>
                    </div>
                </div>
        </div>
        <div class="table-responsive theme-scrollbar">
                <table id="example1" class="table yajra-datatable">
                    <thead>
                        <tr class="text-start text-black-500 fw-bold fs-7 text-uppercase gs-0">
                            <th>No</th>
                            <th>Table Number</th>
                            <th>Action</th>
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
    $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('order.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'table_number', name: 'table_number' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush
