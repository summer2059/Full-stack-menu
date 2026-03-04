@extends('dashboard.layouts.app')

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">

    {{-- ADD USER — admin only --}}
    @can('user.create')
    <div class="card-toolbar mb-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('role.create') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus me-1"></i> Add Role
            </a>
        </div>
    </div>
    @endcan

    <div class="card">
        <div class="card-header border-1 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <h4>Role List</h4>
                </div>
            </div>
        </div>

        <div class="table-responsive theme-scrollbar">
            <table id="example1" class="table yajra-datatable">
                <thead>
                    <tr class="text-start text-black-500 fw-bold fs-7 text-uppercase gs-0">
                        <th>SN</th>
                        <th>Name</th>
                        <th>Actions</th>
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
        ajax: "{{ route('role.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name',        name: 'name' },
            { data: 'action',      name: 'action', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush