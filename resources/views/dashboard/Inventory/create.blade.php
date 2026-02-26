@extends('dashboard.layouts.app')

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="card shadow-sm">
        <div class="card-header border-1 pt-4 pb-2">
            <div class="card-title">
                <h4 class="mb-0">
                    <i class="fa fa-plus-circle text-primary me-2"></i>Add Inventory Item
                </h4>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Item Name --}}
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="e.g. Tomatoes, Olive Oil">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Unit --}}
                    <div class="col-md-6 mb-3">
                        <label for="unit" class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                        <select name="unit" id="unit" class="form-select @error('unit') is-invalid @enderror">
                            <option value="">-- Select Unit --</option>
                            @foreach(['kg', 'g', 'litre', 'ml', 'piece', 'dozen', 'box', 'packet', 'bottle', 'bag'] as $unit)
                                <option value="{{ $unit }}" {{ old('unit') == $unit ? 'selected' : '' }}>
                                    {{ strtoupper($unit) }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Current Stock --}}
                    <div class="col-md-4 mb-3">
                        <label for="current_stock" class="form-label fw-semibold">Opening Stock <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="current_stock" id="current_stock"
                                class="form-control @error('current_stock') is-invalid @enderror"
                                value="{{ old('current_stock', 0) }}"
                                step="0.01" min="0"
                                placeholder="0.00">
                            <span class="input-group-text unit-display">units</span>
                        </div>
                        @error('current_stock')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
                                value="{{ old('minimum_stock', 0) }}"
                                step="0.01" min="0"
                                placeholder="0.00">
                            <span class="input-group-text unit-display">units</span>
                        </div>
                        @error('minimum_stock')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Cost Per Unit --}}
                    <div class="col-md-4 mb-3">
                        <label for="cost_per_unit" class="form-label fw-semibold">Cost Per Unit ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="cost_per_unit" id="cost_per_unit"
                                class="form-control @error('cost_per_unit') is-invalid @enderror"
                                value="{{ old('cost_per_unit', 0) }}"
                                step="0.01" min="0"
                                placeholder="0.00">
                        </div>
                        @error('cost_per_unit')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4 mb-4">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Stock Value Preview --}}
                    <div class="col-12 mb-4">
                        <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                            <i class="fa fa-info-circle"></i>
                            <span>
                                Estimated opening stock value:
                                <strong id="stock_value_preview">$0.00</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fa fa-floppy-o me-1"></i> Save Item
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Linked Menus Section (optional info) --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header border-0 pt-4 pb-2">
            <div class="card-title">
                <h5 class="mb-0 text-muted">
                    <i class="fa fa-lightbulb-o text-warning me-2"></i> How Inventory Works
                </h5>
            </div>
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
                            <div class="text-muted small">After saving, link this item to menu items with required quantities per serving.</div>
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
                            <div class="text-muted small">Stock is automatically deducted when an order status changes to "preparing".</div>
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
                            <div class="text-muted small">You'll be alerted on the dashboard when stock falls below the minimum threshold.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function () {
    // Update unit labels dynamically
    $('#unit').on('change', function () {
        const unit = $(this).val() || 'units';
        $('.unit-display').text(unit);
    });

    // Live stock value preview
    function updateValuePreview() {
        const stock = parseFloat($('#current_stock').val()) || 0;
        const cost  = parseFloat($('#cost_per_unit').val()) || 0;
        const value = (stock * cost).toFixed(2);
        $('#stock_value_preview').text('$' + value);
    }

    $('#current_stock, #cost_per_unit').on('input', updateValuePreview);
});
</script>
@endpush