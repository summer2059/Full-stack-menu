@extends('dashboard.layouts.app')

@php
    $isEdit  = isset($item);
    $title   = $isEdit ? 'Edit Inventory Item' : 'Add Inventory Item';
    $action  = $isEdit ? route('inventory.update', $item->id) : route('inventory.store');
    $icon    = $isEdit ? 'fa-pencil-square-o' : 'fa-plus-circle';
    $btnText = $isEdit ? 'Update Item' : 'Save Item';
@endphp

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="row g-4">

        {{-- ╔══════════════════════════════╗
             ║       MAIN FORM              ║
             ╚══════════════════════════════╝ --}}
        <div class="{{ $isEdit ? 'col-lg-8' : 'col-12' }}">
            <div class="card shadow-sm">

                <div class="card-header border-1 pt-4 pb-2">
                    <div class="card-title">
                        <h4 class="mb-0">
                            <i class="fa {{ $icon }} text-primary me-2"></i>{{ $title }}
                        </h4>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ $action }}" method="POST">
                        @csrf
                        @if($isEdit) @method('PUT') @endif

                        <div class="row">

                            {{-- Item Name --}}
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    Item Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $item->name ?? '') }}"
                                    placeholder="e.g. Tomatoes, Olive Oil">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Unit --}}
                            <div class="col-md-6 mb-3">
                                <label for="unit" class="form-label fw-semibold">
                                    Unit <span class="text-danger">*</span>
                                </label>
                                <select name="unit" id="unit"
                                    class="form-select @error('unit') is-invalid @enderror">
                                    <option value="">-- Select Unit --</option>
                                    @foreach(['kg','g','litre','ml','piece','dozen','box','packet','bottle','bag'] as $u)
                                        <option value="{{ $u }}"
                                            {{ old('unit', $item->unit ?? '') == $u ? 'selected' : '' }}>
                                            {{ strtoupper($u) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Current Stock --}}
                            <div class="col-md-4 mb-3">
                                <label for="current_stock" class="form-label fw-semibold">
                                    {{ $isEdit ? 'Current Stock' : 'Opening Stock' }}
                                    <span class="text-danger">*</span>
                                </label>
                                @if($isEdit)
                                    <p class="text-muted small mb-1">Use Restock for additions — edit only to correct errors.</p>
                                @endif
                                <div class="input-group">
                                    <input type="number" name="current_stock" id="current_stock"
                                        class="form-control @error('current_stock') is-invalid @enderror"
                                        value="{{ old('current_stock', $item->current_stock ?? 0) }}"
                                        step="0.01" min="0" placeholder="0.00">
                                    <span class="input-group-text unit-display">
                                        {{ $item->unit ?? 'units' }}
                                    </span>
                                </div>
                                @error('current_stock')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Minimum Stock --}}
                            <div class="col-md-4 mb-3">
                                <label for="minimum_stock" class="form-label fw-semibold">
                                    Minimum Stock
                                    <span class="text-muted small">(reorder alert)</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="minimum_stock" id="minimum_stock"
                                        class="form-control @error('minimum_stock') is-invalid @enderror"
                                        value="{{ old('minimum_stock', $item->minimum_stock ?? 0) }}"
                                        step="0.01" min="0" placeholder="0.00">
                                    <span class="input-group-text unit-display">
                                        {{ $item->unit ?? 'units' }}
                                    </span>
                                </div>
                                @error('minimum_stock')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Cost Per Unit --}}
                            <div class="col-md-4 mb-3">
                                <label for="cost_per_unit" class="form-label fw-semibold">Cost Per Unit ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="cost_per_unit" id="cost_per_unit"
                                        class="form-control @error('cost_per_unit') is-invalid @enderror"
                                        value="{{ old('cost_per_unit', $item->cost_per_unit ?? 0) }}"
                                        step="0.01" min="0" placeholder="0.00">
                                </div>
                                @error('cost_per_unit')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-4 mb-4">
                                <label for="status" class="form-label fw-semibold">Status</label>
                                <select name="status" id="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status', $item->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $item->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Stock Value Preview --}}
                            <div class="col-12 mb-4">
                                <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                                    <i class="fa fa-info-circle"></i>
                                    <span>
                                        {{ $isEdit ? 'Current' : 'Estimated opening' }} stock value:
                                        <strong id="stock_value_preview">
                                            ${{ number_format(($item->current_stock ?? 0) * ($item->cost_per_unit ?? 0), 2) }}
                                        </strong>
                                    </span>
                                </div>
                            </div>

                        </div>{{-- end .row --}}

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-floppy-o me-1"></i> {{ $btnText }}
                            </button>
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>{{-- end main form col --}}

        {{-- ╔══════════════════════════════╗
             ║   EDIT-ONLY SIDEBAR          ║
             ╚══════════════════════════════╝ --}}
        @if($isEdit)
        <div class="col-lg-4">

            {{-- Stock Status --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Stock Status</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Current Stock</span>
                        <span class="fw-semibold">{{ $item->current_stock }} {{ $item->unit }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Minimum Required</span>
                        <span class="fw-semibold">{{ $item->minimum_stock }} {{ $item->unit }}</span>
                    </div>
                    @php
                        $pct      = $item->minimum_stock > 0
                            ? min(100, ($item->current_stock / $item->minimum_stock) * 100)
                            : 100;
                        $barColor = $pct >= 100 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-{{ $barColor }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">{{ round($pct) }}% of min threshold</small>
                        @if($item->isLowStock())
                            <small class="text-danger fw-semibold">⚠ Low Stock</small>
                        @else
                            <small class="text-success fw-semibold">✓ Sufficient</small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Restock --}}
            @can('inventory.restock')
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Quick Restock</h6>
                    <form action="{{ route('inventory.restock') }}" method="POST">
                        @csrf
                        <input type="hidden" name="inventory_item_id" value="{{ $item->id }}">
                        <div class="mb-2">
                            <label class="form-label small">Add Quantity ({{ $item->unit }})</label>
                            <input type="number" name="quantity"
                                class="form-control form-control-sm"
                                step="0.01" min="0.01"
                                placeholder="e.g. 10" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Note (optional)</label>
                            <input type="text" name="note"
                                class="form-control form-control-sm"
                                placeholder="e.g. Weekly delivery">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fa fa-plus me-1"></i> Restock Now
                        </button>
                    </form>
                </div>
            </div>
            @endcan

            {{-- Recent Activity --}}
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-3 pb-2">
                    <h6 class="mb-0 fw-bold">Recent Activity</h6>
                </div>
                <div class="card-body p-0">
                    @forelse($item->logs()->latest()->take(6)->get() as $log)
                    <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
                        <div class="mt-1">
                            @if($log->type === 'restock')
                                <span class="badge bg-success-subtle text-success">+</span>
                            @elseif($log->type === 'consumption')
                                <span class="badge bg-danger-subtle text-danger">-</span>
                            @elseif($log->type === 'waste')
                                <span class="badge bg-warning-subtle text-warning">W</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">~</span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="small fw-semibold text-capitalize">{{ $log->type }}</div>
                            <div class="small text-muted">{{ $log->note ?? '—' }}</div>
                            <div class="small text-muted">After: {{ $log->stock_after }} {{ $item->unit }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small {{ $log->quantity >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                {{ $log->quantity >= 0 ? '+' : '' }}{{ $log->quantity }}
                            </div>
                            <div class="text-muted" style="font-size:11px;">
                                {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4 small">No activity yet.</div>
                    @endforelse
                </div>
            </div>

        </div>{{-- end sidebar col --}}
        @endif

    </div>{{-- end .row --}}

    {{-- How Inventory Works — create mode only --}}
    @if(!$isEdit)
    <div class="card shadow-sm mt-4">
        <div class="card-header border-0 pt-4 pb-2">
            <h5 class="mb-0 text-muted">
                <i class="fa fa-lightbulb-o text-warning me-2"></i> How Inventory Works
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 flex-shrink-0">
                            <i class="fa fa-cutlery text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Link to Menus</div>
                            <div class="text-muted small">After saving, link this item to menu items via Recipes.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 flex-shrink-0">
                            <i class="fa fa-minus-circle text-success"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Auto Deduction</div>
                            <div class="text-muted small">Stock deducts automatically when an order is marked "preparing".</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 flex-shrink-0">
                            <i class="fa fa-bell text-warning"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">Low Stock Alert</div>
                            <div class="text-muted small">Dashboard alerts when stock falls below the minimum threshold.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('js')
<script>
$(function () {
    $('#unit').on('change', function () {
        $('.unit-display').text($(this).val() || 'units');
    });

    function updatePreview() {
        const stock = parseFloat($('#current_stock').val()) || 0;
        const cost  = parseFloat($('#cost_per_unit').val()) || 0;
        $('#stock_value_preview').text('$' + (stock * cost).toFixed(2));
    }

    $('#current_stock, #cost_per_unit').on('input', updatePreview);
});
</script>
@endpush