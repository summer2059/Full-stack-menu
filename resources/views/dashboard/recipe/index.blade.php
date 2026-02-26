{{-- resources/views/dashboard/recipe/index.blade.php --}}
@extends('dashboard.layouts.app')

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6 pb-4">
            <div class="card-title">
                <h4 class="mb-0">
                    <i class="fa fa-cutlery text-warning me-2"></i> Recipe Management
                </h4>
                <span class="text-muted small ms-2">Define ingredients for each menu item</span>
            </div>
        </div>
        <div class="table-responsive">
            <table id="recipeTable" class="table yajra-datatable">
                <thead>
                    <tr class="text-uppercase fw-bold fs-7 text-muted">
                        <th>S.N</th>
                        <th>Menu Item</th>
                        <th>Category</th>
                        <th>Ingredients</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function () {
    $('#recipeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('recipe.index') }}",
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'title' },
            { data: 'category' },
            { data: 'ingredients_count' },
            { data: 'action', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush