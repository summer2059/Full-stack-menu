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

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <a href="{{ route('inventory.create') }}" class="btn btn-sm btn-primary"> + Add Item </a>

        <div class="d-flex align-items-center gap-2 flex-wrap">

            <div class="d-flex align-items-center gap-1">
                <small class="text-muted text-nowrap">Show:</small>
                <select wire:model.live="perPage" class="form-select form-select-sm" style="width:75px;">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <div class="d-flex align-items-center gap-1">
                <small class="text-muted text-nowrap">Stock:</small>
                <select wire:model.live="stockFilter" class="form-select form-select-sm" style="min-width:140px;">
                    <option value="">All Items</option>
                    <option value="low">Low Stock</option>
                    <option value="out">Out of Stock</option>
                </select>
            </div>

            <div class="position-relative">
                <input type="text" wire:model.live.debounce.400ms="search" class="form-control form-control-sm ps-4" placeholder="Search item name..." style="min-width:210px;">
                <span class="position-absolute top-50 start-0 translate-middle-y ps-2 text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415l-3.868-3.833zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </span>
                @if($search)
                    <button wire:click="$set('search','')" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent text-muted pe-2" style="z-index:2;line-height:1;">✕</button>
                @endif
            </div>

            @if($search || $stockFilter)
                <button wire:click="$set('search',''); $set('stockFilter','')" class="btn btn-sm btn-outline-secondary">Clear</button>
            @endif
        </div>
    </div>

    <div wire:loading.flex class="align-items-center gap-2 mb-2 text-primary" style="font-size:0.85rem;">
        <div class="spinner-border spinner-border-sm"></div>
        <span>Loading...</span>
    </div>

    <div class="table-responsive theme-scrollbar">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="text-uppercase fw-bold fs-7" style="background:#f8f9fa;">
                <tr>
                    <th style="width:55px;">#</th>
                    <th wire:click="sortBy('name')" style="cursor:pointer;user-select:none;">
                        Item Name
                        @if($sortField==='name') <span class="text-primary">{{ $sortDir==='asc'?'▲':'▼' }}</span>
                        @else <span class="text-muted" style="font-size:.7rem;">⇅</span> @endif
                    </th>
                    <th wire:click="sortBy('unit')" style="cursor:pointer;user-select:none;width:80px;">
                        Unit
                        @if($sortField==='unit') <span class="text-primary">{{ $sortDir==='asc'?'▲':'▼' }}</span>
                        @else <span class="text-muted" style="font-size:.7rem;">⇅</span> @endif
                    </th>
                    <th wire:click="sortBy('current_stock')" style="cursor:pointer;user-select:none;">
                        Current Stock
                        @if($sortField==='current_stock') <span class="text-primary">{{ $sortDir==='asc'?'▲':'▼' }}</span>
                        @else <span class="text-muted" style="font-size:.7rem;">⇅</span> @endif
                    </th>
                    <th wire:click="sortBy('minimum_stock')" style="cursor:pointer;user-select:none;">
                        Min Stock
                        @if($sortField==='minimum_stock') <span class="text-primary">{{ $sortDir==='asc'?'▲':'▼' }}</span>
                        @else <span class="text-muted" style="font-size:.7rem;">⇅</span> @endif
                    </th>
                    <th wire:click="sortBy('cost_per_unit')" style="cursor:pointer;user-select:none;">
                        Cost/Unit
                        @if($sortField==='cost_per_unit') <span class="text-primary">{{ $sortDir==='asc'?'▲':'▼' }}</span>
                        @else <span class="text-muted" style="font-size:.7rem;">⇅</span> @endif
                    </th>
                    <th style="width:120px;">Stock Status</th>
                    <th style="width:100px;">Status</th>
                    <th style="width:160px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr wire:key="inv-{{ $item->id }}">
                        <td>{{ $items->firstItem() + $index }}</td>
                        <td class="fw-semibold">{{ $item->name }}</td>
                        <td><span class="badge bg-light text-dark">{{ strtoupper($item->unit) }}</span></td>
                        <td>
                            <span class="fw-semibold {{ $item->current_stock <= 0 ? 'text-danger' : ($item->isLowStock() ? 'text-warning' : '') }}">
                                {{ number_format($item->current_stock, 2) }} {{ $item->unit }}
                            </span>
                        </td>
                        <td>{{ number_format($item->minimum_stock, 2) }} {{ $item->unit }}</td>
                        <td>${{ number_format($item->cost_per_unit, 2) }}</td>

                        <td>
                            @if($item->current_stock <= 0)
                                <span class="badge px-3 py-2" style="background:#fee2e2;color:#991b1b;font-size:.75rem;">Out of Stock</span>
                            @elseif($item->isLowStock())
                                <span class="badge px-3 py-2" style="background:#fef3c7;color:#92400e;font-size:.75rem;">⚠ Low Stock</span>
                            @else
                                <span class="badge px-3 py-2" style="background:#d1fae5;color:#065f46;font-size:.75rem;">✓ OK</span>
                            @endif
                        </td>

                        <td>
                            <div wire:click="toggleStatus({{ $item->id }})" wire:loading.class="opacity-50" wire:target="toggleStatus({{ $item->id }})" title="Click to toggle" style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;" >
                                <div style=" position:relative;width:46px;height:24px;border-radius:12px; background:{{ $item->status==1 ? '#4CAF50' : '#ccc' }}; transition:background .3s;flex-shrink:0; ">
                                    <div style=" position:absolute;top:3px; left:{{ $item->status==1 ? '25px' : '3px' }}; width:18px;height:18px;border-radius:50%; background:white;box-shadow:0 1px 3px rgba(0,0,0,.3); transition:left .3s;"></div>
                                </div>
                                <small style="font-size:.78rem;font-weight:600;color:{{ $item->status==1 ? '#4CAF50' : '#999' }};">
                                    {{ $item->status==1 ? 'On' : 'Off' }}
                                </small>
                            </div>
                        </td>

                        <td class="text-center">
                            <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-primary me-1">Edit</a>
                            <button
                                wire:click="openRestock({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->unit }}')"
                                class="btn btn-sm btn-success me-1"
                            >Restock</button>
                            <button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <div style="font-size:2rem;">📭</div>
                            <div class="mt-1">No inventory items found.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <small class="text-muted">
            @if($items->total() > 0)
                Showing <strong>{{ $items->firstItem() }}</strong> to <strong>{{ $items->lastItem() }}</strong> of <strong>{{ $items->total() }}</strong> entries
            @else No entries found @endif
        </small>
        <div>{{ $items->links() }}</div>
    </div>

    @if($showRestock)
        <div style="position:fixed;inset:0;z-index:1055;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;">
            <div class="card shadow-lg" style="width:100%;max-width:420px;border-radius:14px;overflow:hidden;">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">Restock: {{ $restockName }}</h5>
                    <button wire:click="closeRestock" class="btn-close"></button>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quantity to Add ({{ $restockUnit }}) <span class="text-danger">*</span></label>
                        <input type="number" wire:model.live="restockQty" class="form-control @error('restockQty') is-invalid @enderror" step="0.01" min="0.01" placeholder="e.g. 10">
                        @error('restockQty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Note (optional)</label>
                        <input type="text" wire:model="restockNote" class="form-control" placeholder="e.g. Weekly delivery">
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <button wire:click="closeRestock" class="btn btn-secondary">Cancel</button>
                    <button wire:click="confirmRestock" class="btn btn-success" wire:loading.attr="disabled" wire:target="confirmRestock">
                        <span wire:loading wire:target="confirmRestock"><span class="spinner-border spinner-border-sm me-1"></span></span>
                        Confirm Restock
                    </button>
                </div>
            </div>
        </div>
    @endif
    @if($showConfirm)
        <div style="position:fixed;inset:0;z-index:1055;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;">
            <div class="card shadow-lg" style="width:100%;max-width:430px;border-radius:14px;overflow:hidden;animation:fadeInScale .2s ease;">
                <div class="card-body p-4 text-center">
                    <div style="font-size:3rem;">🗑️</div>
                    <h5 class="fw-bold mt-3 mb-1">Confirm Delete</h5>
                    <p class="text-muted mb-4">Are you sure you want to delete this item?<br><strong>This action cannot be undone.</strong></p>
                    <div class="d-flex justify-content-center gap-3">
                        <button wire:click="cancelDelete" class="btn btn-light px-4">Cancel</button>
                        <button wire:click="delete" class="btn btn-danger px-4" wire:loading.attr="disabled" wire:target="delete">
                            <span wire:loading wire:target="delete"><span class="spinner-border spinner-border-sm me-1"></span></span>
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <style>
            @keyframes fadeInScale { from{opacity:0;transform:scale(.92)} to{opacity:1;transform:scale(1)} }
        </style>
    @endif
</div>