@extends('dashboard.layouts.app')

@push('css')
    <style>
        .toggle-status { position: relative; display: inline-block; width: 60px; height: 34px; }
        .toggle-status input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: 0.4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 26px; width: 26px; border-radius: 50%; left: 4px; bottom: 4px; background-color: white; transition: 0.4s; }
        input:checked + .slider { background-color: #4CAF50; }
        input:checked + .slider:before { transform: translateX(26px); }
        .off-text { position: absolute; top: 6px; left: 10px; color: #fff; font-size: 12px; font-weight: bold; }
        .on-text  { position: absolute; top: 6px; right: 10px; color: #fff; font-size: 12px; font-weight: bold; }
        .stock-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .stock-ok      { background: #d1fae5; color: #065f46; }
        .stock-low     { background: #fee2e2; color: #991b1b; }
        .stock-warning { background: #fef3c7; color: #92400e; }
        .stat-card { border-radius: 12px; padding: 20px 24px; border: none; position: relative; overflow: hidden; }
        .stat-card::after { content: ''; position: absolute; right: -15px; top: -15px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.1; }
        .stat-card.total { background: #eff6ff; } .stat-card.total::after { background: #2563eb; }
        .stat-card.low   { background: #fef2f2; } .stat-card.low::after   { background: #dc2626; }
        .stat-card.value { background: #f0fdf4; } .stat-card.value::after { background: #16a34a; }
        .stat-card.today { background: #fffbeb; } .stat-card.today::after { background: #d97706; }
    </style>
@endpush

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">

    {{-- Stats Row --}}
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card total shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="fa fa-archive fa-lg text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Items</div>
                        <div class="fs-3 fw-bold text-primary">{{ $totalItems ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card low shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="fa fa-exclamation-triangle fa-lg text-danger"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Low Stock</div>
                        <div class="fs-3 fw-bold text-danger">{{ $lowStockCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card value shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="fa fa-usd fa-lg text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Stock Value</div>
                        <div class="fs-3 fw-bold text-success">${{ number_format($stockValue ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card today shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="fa fa-line-chart fa-lg text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Today's Usage</div>
                        <div class="fs-3 fw-bold text-warning">{{ $todayUsageCount ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Low Stock Alert --}}
    @if(isset($lowStock) && $lowStock->count() > 0)
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-3 mb-5" role="alert">
        <i class="fa fa-exclamation-circle mt-1"></i>
        <div>
            <strong>Low Stock Alert!</strong> The following items need restocking:
            <strong>{{ $lowStock->pluck('name')->implode(', ') }}</strong>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Main Table Card --}}
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6 pb-4">
            <div class="card-title">
                <h4 class="mb-0">Inventory List</h4>
            </div>
            <div class="card-toolbar">
                {{-- ADD ITEM — inventory_manager + admin --}}
                @can('inventory.create')
                <a href="{{ route('inventory.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus me-1"></i> Add Item
                </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive theme-scrollbar">
            <table id="inventoryTable" class="table yajra-datatable">
                <thead>
                    <tr class="text-start text-black-500 fw-bold fs-7 text-uppercase gs-0">
                        <th>S.N</th>
                        <th>Item Name</th>
                        <th>Unit</th>
                        <th>Current Stock</th>
                        <th>Min Stock</th>
                        <th>Cost/Unit</th>
                        <th>Stock Status</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Restock Modal — inventory_manager + admin only --}}
@can('inventory.restock')
<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-plus-circle text-success me-2"></i>Restock Item
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.restock') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="inventory_item_id" id="restock_item_id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item</label>
                        <input type="text" id="restock_item_name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Quantity to Add <span id="restock_unit_label" class="text-muted"></span>
                        </label>
                        <input type="number" name="quantity" class="form-control"
                               step="0.01" min="0.01" required placeholder="e.g. 10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Note (optional)</label>
                        <input type="text" name="note" class="form-control" placeholder="e.g. Weekly delivery">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check me-1"></i> Confirm Restock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection

@push('js')
<script>
$(function () {
    // Pass permission flags from Blade to JS
    const canEdit    = @json(auth()->user()->can('inventory.update'));
    const canRestock = @json(auth()->user()->can('inventory.restock'));
    const canDelete  = @json(auth()->user()->can('inventory.delete'));

    var table = $('#inventoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('inventory.index') }}",
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false },
            { data: 'name',           name: 'name' },
            { data: 'unit',           name: 'unit' },
            {
                data: 'current_stock', name: 'current_stock',
                render: (data, type, row) => `<span class="fw-semibold">${parseFloat(data).toFixed(2)} ${row.unit}</span>`
            },
            {
                data: 'minimum_stock', name: 'minimum_stock',
                render: (data, type, row) => `${parseFloat(data).toFixed(2)} ${row.unit}`
            },
            {
                data: 'cost_per_unit', name: 'cost_per_unit',
                render: data => `$${parseFloat(data).toFixed(2)}`
            },
            { data: 'stock_status', name: 'stock_status', orderable: false, searchable: false },
            {
                data: 'status', name: 'status', orderable: false, searchable: false,
                render: (data, type, row) => `
                    <label class="toggle-status">
                        <input type="checkbox" class="status-toggle"
                            data-id="${row.id}" data-model="InventoryItem"
                            ${parseInt(data) === 1 ? 'checked' : ''} />
                        <span class="slider">
                            <span class="off-text">Off</span>
                            <span class="on-text">On</span>
                        </span>
                    </label>`
            },
            {
                data: 'action', name: 'action', orderable: false, searchable: false,
                // Actions are already permission-gated in the controller's addColumn callback
                // The JS flags below are for any client-side conditional rendering if needed
                render: function(data) { return data; }
            },
        ]
    });

    // Open restock modal — only shown if user has permission (modal doesn't exist otherwise)
    $(document).on('click', '.restock-btn', function () {
        if (!canRestock) return;
        $('#restock_item_id').val($(this).data('id'));
        $('#restock_item_name').val($(this).data('name'));
        $('#restock_unit_label').text('(' + $(this).data('unit') + ')');
        new bootstrap.Modal(document.getElementById('restockModal')).show();
    });

    // Toggle status — inventory_manager + admin
    $(document).on('change', '.status-toggle', function () {
        const id  = $(this).data('id');
        const model = $(this).data('model');
        const btn   = $(this);

        $.ajax({
            url: `{{ url('dashboard/toggle-status') }}/${model}/${id}`,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (!res.success) {
                    btn.prop('checked', !btn.prop('checked'));
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function () {
                btn.prop('checked', !btn.prop('checked'));
                Swal.fire('Error', 'Something went wrong.', 'error');
            }
        });
    });
});
</script>
@endpush