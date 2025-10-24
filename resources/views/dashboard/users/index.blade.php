@extends('dashboard.layouts.app')
@section('content')
<div id="kt_app_content_container" class="app-container  container-xxl ">
    {{-- <h2>Users</h2>

    <a href="{{ route('user.create') }}" class="btn btn-success mb-3">Add User</a> --}}

    <div class="card-toolbar mb-4">
        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">

            <a href="{{ route('user.create') }}" class="btn btn-sm btn-primary">
                Add User
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header border-1 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <h4>User List</h4>
                </div>
            </div>
        </div>
        <div class="table-responsive theme-scrollbar">
        <table id="example1" class="table yajra-datatable">
            <thead>
                <tr class="text-start text-black-500 fw-bold fs-7 text-uppercase gs-0">
                    <th>SN</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role(s)</th>
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
        $(function() {

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });


        });
    </script>
@endpush
