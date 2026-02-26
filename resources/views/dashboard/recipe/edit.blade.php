{{-- resources/views/dashboard/recipe/edit.blade.php --}}
@extends('dashboard.layouts.app')

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-4 pb-2">
            <div class="card-title">
                <h4 class="mb-0">
                    <i class="fa fa-cutlery text-warning me-2"></i>
                    Recipe: <span class="text-primary">{{ $menu->title }}</span>
                </h4>
                <span class="text-muted small ms-2">Quantities are per <strong>1 serving</strong></span>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('recipe.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('recipe.update', $menu->id) }}" method="POST">
                @csrf

                <div class="table-responsive mb-3">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-uppercase fw-bold fs-7 text-muted">
                                <th style="min-width:250px">Ingredient</th>
                                <th style="min-width:160px">Qty per Serving</th>
                                <th>Unit</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody id="ingredientRows">

                            @forelse($menu->inventoryItems as $index => $item)
                            <tr class="ingredient-row">
                                <td>
                                    <select name="ingredients[{{ $index }}][item_id]"
                                        class="form-select item-select" required>
                                        <option value="">-- Select --</option>
                                        @foreach($inventoryItems as $inv)
                                            <option value="{{ $inv->id }}"
                                                data-unit="{{ $inv->unit }}"
                                                {{ $item->id == $inv->id ? 'selected' : '' }}>
                                                {{ $inv->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number"
                                        name="ingredients[{{ $index }}][quantity]"
                                        class="form-control"
                                        step="0.001" min="0.001"
                                        value="{{ $item->pivot->quantity_required }}" required>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 unit-label">
                                        {{ $item->unit }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-light-danger remove-row">
                                        <i class="fa fa-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr class="ingredient-row">
                                <td>
                                    <select name="ingredients[0][item_id]"
                                        class="form-select item-select" required>
                                        <option value="">-- Select Ingredient --</option>
                                        @foreach($inventoryItems as $inv)
                                            <option value="{{ $inv->id }}" data-unit="{{ $inv->unit }}">
                                                {{ $inv->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="ingredients[0][quantity]"
                                        class="form-control" step="0.001" min="0.001"
                                        placeholder="e.g. 0.100" required>
                                </td>
                                <td><span class="badge bg-light text-dark unit-label">—</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-light-danger remove-row">
                                        <i class="fa fa-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <button type="button" id="addRow" class="btn btn-sm btn-outline-primary mb-5">
                    <i class="fa fa-plus me-1"></i> Add Ingredient
                </button>

                {{-- Live cost preview --}}
                <div class="alert alert-light border mb-4" id="costPreview" style="display:none">
                    <strong><i class="fa fa-calculator me-2 text-primary"></i>Estimated ingredient cost per serving:</strong>
                    <span id="costValue" class="text-primary fw-bold ms-2">$0.00</span>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-6">
                        <i class="fa fa-floppy-o me-1"></i> Save Recipe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden template row --}}
<template id="rowTemplate">
    <tr class="ingredient-row">
        <td>
            <select name="ingredients[__INDEX__][item_id]" class="form-select item-select" required>
                <option value="">-- Select Ingredient --</option>
                @foreach($inventoryItems as $inv)
                    <option value="{{ $inv->id }}"
                        data-unit="{{ $inv->unit }}"
                        data-cost="{{ $inv->cost_per_unit }}">
                        {{ $inv->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="ingredients[__INDEX__][quantity]"
                class="form-control qty-input" step="0.001" min="0.001"
                placeholder="e.g. 0.100" required>
        </td>
        <td><span class="badge bg-light text-dark unit-label">—</span></td>
        <td>
            <button type="button" class="btn btn-sm btn-light-danger remove-row">
                <i class="fa fa-trash text-danger"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('js')
<script>
$(function () {
    let rowIndex = {{ max($menu->inventoryItems->count(), 1) }};

    // Add row
    $('#addRow').on('click', function () {
        let template = document.getElementById('rowTemplate').innerHTML;
        template = template.replace(/__INDEX__/g, rowIndex);
        $('#ingredientRows').append(template);
        rowIndex++;
        recalcCost();
    });

    // Remove row
    $(document).on('click', '.remove-row', function () {
        if ($('.ingredient-row').length > 1) {
            $(this).closest('tr').remove();
            recalcCost();
        } else {
            Swal.fire('Warning', 'At least one ingredient is required.', 'warning');
        }
    });

    // Show unit on select
    $(document).on('change', '.item-select', function () {
        const selected = $(this).find(':selected');
        const unit = selected.data('unit') || '—';
        $(this).closest('tr').find('.unit-label').text(unit);
        recalcCost();
    });

    // Recalc on qty change
    $(document).on('input', '.qty-input', recalcCost);

    function recalcCost() {
        let total = 0;
        let valid = false;

        $('.ingredient-row').each(function () {
            const select = $(this).find('.item-select');
            const qty    = parseFloat($(this).find('input[type=number]').val()) || 0;
            const cost   = parseFloat(select.find(':selected').data('cost')) || 0;
            if (qty > 0 && cost > 0) {
                total += qty * cost;
                valid = true;
            }
        });

        if (valid) {
            $('#costValue').text('$' + total.toFixed(4));
            $('#costPreview').show();
        } else {
            $('#costPreview').hide();
        }
    }

    recalcCost();
});
</script>
@endpush