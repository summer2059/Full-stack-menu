@extends('dashboard.layouts.app')

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="row g-4">

        {{-- Left: Livewire Edit Form --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header border-1 pt-4 pb-2">
                    <div class="card-title">
                        <h4 class="mb-0">
                            <i class="fa fa-pencil-square-o text-primary me-2"></i>Edit Inventory Item
                        </h4>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Pass item id so component loads existing data via mount() --}}
                    @livewire('dashboard.inventory.form', ['id' => $item->id])
                </div>
            </div>
        </div>

        {{-- Right: Sidebar Info --}}
        <div class="col-lg-4">

            {{-- Stock Status Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Stock Status</h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Current Stock</span>
                        <span class="fw-semibold">{{ $item->current_stock }} {{ $item->unit }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
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
                        <div
                            class="progress-bar bg-{{ $barColor }}"
                            style="width:{{ $pct }}%"
                            title="{{ round($pct) }}% of minimum"
                        ></div>
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

            {{-- Quick Restock Form --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Quick Restock</h6>
                    <form action="{{ route('inventory.restock') }}" method="POST">
                        @csrf
                        <input type="hidden" name="inventory_item_id" value="{{ $item->id }}">
                        <div class="mb-2">
                            <label class="form-label small">Add Quantity ({{ $item->unit }})</label>
                            <input
                                type="number"
                                name="quantity"
                                class="form-control form-control-sm"
                                step="0.01" min="0.01"
                                placeholder="e.g. 10"
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Note (optional)</label>
                            <input type="text" name="note" class="form-control form-control-sm" placeholder="e.g. Weekly delivery">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fa fa-plus me-1"></i> Restock Now
                        </button>
                    </form>
                </div>
            </div>

            {{-- Recent Activity Logs --}}
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
                                <div class="text-muted" style="font-size:11px;">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4 small">No activity yet.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection