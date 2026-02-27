<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                <input type="text" wire:model.live="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Tomatoes, Olive Oil">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                <select wire:model.live="unit" class="form-select @error('unit') is-invalid @enderror">
                    <option value="">-- Select Unit --</option>
                    @foreach(['kg', 'g', 'litre', 'ml', 'piece', 'dozen', 'box', 'packet', 'bottle', 'bag'] as $u)
                        <option value="{{ $u }}">{{ strtoupper($u) }}</option>
                    @endforeach
                </select>
                @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">
                    {{ $itemId ? 'Current Stock' : 'Opening Stock' }}
                    <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="number" wire:model.live="current_stock" class="form-control @error('current_stock') is-invalid @enderror" step="0.01" min="0" placeholder="0.00">
                    <span class="input-group-text"> {{ $unit ?: 'units' }} </span>
                </div>
                @error('current_stock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                @if($itemId)
                    <div class="form-text text-muted">Use Restock for additions — edit only for corrections.</div>
                @endif
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">
                    Minimum Stock
                    <span class="text-muted small">(reorder alert)</span>
                </label>
                <div class="input-group">
                    <input type="number" wire:model.live="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" step="0.01" min="0" placeholder="0.00" >
                    <span class="input-group-text">
                        {{ $unit ?: 'units' }}
                    </span>
                </div>
                @error('minimum_stock') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Cost Per Unit (Rs)</label>
                <div class="input-group">
                    <span class="input-group-text">Rs</span>
                    <input type="number" wire:model.live="cost_per_unit" class="form-control @error('cost_per_unit') is-invalid @enderror" step="0.01" min="0" placeholder="0.00" >
                </div>
                @error('cost_per_unit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mb-4">
                <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                    <i class="fa fa-info-circle"></i>
                    <span>
                        {{ $itemId ? 'Current' : 'Estimated opening' }} stock value:
                        <strong>${{ number_format($stockValuePreview, 2) }}</strong>
                    </span>
                </div>
            </div>

        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary me-2" wire:loading.attr="disabled" wire:target="save" >
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                </span>
                <i class="fa fa-floppy-o me-1"></i>
                {{ $itemId ? 'Update Item' : 'Save Item' }}
            </button>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
</div>