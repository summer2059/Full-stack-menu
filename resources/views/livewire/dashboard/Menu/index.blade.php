<div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <a href="{{ route('menu.create') }}" class="btn btn-sm btn-primary"> Add Menu </a>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="d-flex align-items-center gap-1">
                <small class="text-muted text-nowrap">Show:</small>
                <select wire:model.live="perPage" class="form-select form-select-sm" style="width: 75px;">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <div class="d-flex align-items-center gap-1">
                <small class="text-muted text-nowrap">Category:</small>
                <select wire:model.live="categoryFilter" class="form-select form-select-sm" style="min-width: 160px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="position-relative">
                <input type="text" wire:model.live.debounce.400ms="search" class="form-control form-control-sm ps-4" placeholder="Search title or category..." style="min-width: 230px;" >
                <span class="position-absolute top-50 start-0 translate-middle-y ps-2 text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415l-3.868-3.833zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </span>
                @if($search)
                    <button wire:click="$set('search', '')" class="btn btn-sm position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent text-muted pe-2" style="z-index: 2; line-height: 1;" >✕</button>
                @endif
            </div>
        </div>
    </div>

    <div wire:loading.flex class="align-items-center gap-2 mb-2 text-primary" style="font-size: 0.85rem;">
        <div class="spinner-border spinner-border-sm" role="status"></div>
        <span>Loading...</span>
    </div>

    <div class="table-responsive theme-scrollbar">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="text-uppercase fw-bold fs-7" style="background: #f8f9fa;">
                <tr>
                    <th style="width: 55px;">SN</th>
                    <th wire:click="sortBy('title')" style="cursor: pointer; user-select: none; white-space: nowrap;">
                        Title
                        @if($sortField === 'title')
                            <span class="text-primary">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                        @else
                            <span class="text-muted" style="font-size: 0.7rem;">⇅</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('menu_category_id')" style="cursor: pointer; user-select: none; white-space: nowrap;">
                        Category
                        @if($sortField === 'menu_category_id')
                            <span class="text-primary">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                        @else
                            <span class="text-muted" style="font-size: 0.7rem;">⇅</span>
                        @endif
                    </th>
                    <th style="width: 100px;">Image</th>
                    <th wire:click="sortBy('rating')" style="cursor: pointer; user-select: none; white-space: nowrap; width: 90px;">
                        Rating
                        @if($sortField === 'rating')
                            <span class="text-primary">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                        @else
                            <span class="text-muted" style="font-size: 0.7rem;">⇅</span>
                        @endif
                    </th>
                    <th wire:click="sortBy('status')" style="cursor: pointer; user-select: none; white-space: nowrap; width: 110px;">
                        Status
                        @if($sortField === 'status')
                            <span class="text-primary">{{ $sortDir === 'asc' ? '▲' : '▼' }}</span>
                        @else
                            <span class="text-muted" style="font-size: 0.7rem;">⇅</span>
                        @endif
                    </th>
                    <th style="width: 140px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($menus as $index => $menu)
                    <tr wire:key="menu-{{ $menu->id }}">
                        <td>{{ $menus->firstItem() + $index }}</td>
                        <td>{{ $menu->title }}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $menu->menuCategory?->title ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if ($menu->image)
                                <img src="{{ asset('uploads/images/' . $menu->image) }}" alt="{{ $menu->title }}" style="width: 65px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                            @else
                                <img src="{{ asset('uploads/image.png') }}" alt="default" style="width: 65px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                            @endif
                        </td>
                        <td>
                            <span class="text-warning fw-semibold">
                                ★ {{ number_format($menu->rating, 1) }}
                            </span>
                        </td>
                        <td>
                            <div wire:click="toggleStatus({{ $menu->id }})" wire:loading.class="opacity-50" wire:target="toggleStatus({{ $menu->id }})" title="Click to toggle status" style="cursor: pointer; display: inline-flex; align-items: center; gap: 6px;" >
                                <div style="position: relative; width: 46px; height: 24px; border-radius: 12px; background: {{ $menu->status == 1 ? '#4CAF50' : '#ccc' }}; transition: background 0.3s ease; flex-shrink: 0; ">
                                    <div style="position: absolute; top: 3px; left: {{ $menu->status == 1 ? '25px' : '3px' }}; width: 18px; height: 18px; border-radius: 50%; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.3); transition: left 0.3s ease; "></div>
                                </div>
                                <small style="font-size: 0.78rem; font-weight: 600; color: {{ $menu->status == 1 ? '#4CAF50' : '#999' }};">
                                    {{ $menu->status == 1 ? 'On' : 'Off' }}
                                </small>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-sm btn-primary me-1"> Edit </a>
                            <button wire:click="confirmDelete({{ $menu->id }})" class="btn btn-sm btn-danger"> Delete </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <div style="font-size: 2rem;">📭</div>
                            <div class="mt-1">No menus found.</div>
                            @if($search)
                                <small>Try clearing your search.</small>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <small class="text-muted">
            @if($menus->total() > 0)
                Showing <strong>{{ $menus->firstItem() }}</strong>
                to <strong>{{ $menus->lastItem() }}</strong>
                of <strong>{{ $menus->total() }}</strong> entries
            @else
                No entries found
            @endif
        </small>
        <div>{{ $menus->links() }}</div>
    </div>

    @if ($showConfirm)
        <div style="position: fixed; inset: 0; z-index: 1055; background: rgba(0,0,0,0.55); display: flex; align-items: center; justify-content: center;">
            <div class="card shadow-lg" style="width: 100%; max-width: 430px; border-radius: 14px; overflow: hidden; animation: fadeInScale 0.2s ease;">
                <div class="card-body p-4 text-center">
                    <div style="font-size: 3rem; line-height: 1;">🗑️</div>
                    <h5 class="fw-bold mt-3 mb-1">Confirm Delete</h5>
                    <p class="text-muted mb-4">
                        Are you sure you want to delete this menu item?
                        <br><strong>This action cannot be undone.</strong>
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button wire:click="cancelDelete" class="btn btn-light px-4" wire:loading.attr="disabled"> Cancel </button>
                        <button wire:click="delete" class="btn btn-danger px-4" wire:loading.attr="disabled" wire:target="delete">
                            <span wire:loading wire:target="delete">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            </span>
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <style>
            @keyframes fadeInScale {
                from { opacity: 0; transform: scale(0.92); }
                to   { opacity: 1; transform: scale(1); }
            }
        </style>
    @endif
</div>