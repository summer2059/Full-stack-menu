@extends('dashboard.layouts.app')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
@endpush

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">✅ Completed Orders</h4>
            <a href="{{ route('order.index') }}" class="btn btn-secondary">⬅ Back</a>
        </div>
        <div class="card-body">
            <table class="table yajra-datatable table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script>
$(function () {
    $('.yajra-datatable').DataTable({
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
