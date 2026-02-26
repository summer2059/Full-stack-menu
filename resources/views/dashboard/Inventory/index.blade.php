@extends('dashboard.layouts.app')

@push('css')
<style>
    nav[role="navigation"] .pagination { margin-bottom: 0; }
    nav[role="navigation"] .pagination .page-link { border-radius: 6px; margin: 0 2px; font-size: 0.85rem; }
    .stat-card { border-radius:12px; padding:20px 24px; border:none; }
    .stat-card.total  { background:#eff6ff; }
    .stat-card.low    { background:#fef2f2; }
    .stat-card.value  { background:#f0fdf4; }
    .stat-card.today  { background:#fffbeb; }
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
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-3 mb-5">
        <i class="fa fa-exclamation-circle mt-1"></i>
        <div>
            <strong>Low Stock Alert!</strong> The following items need restocking:
            <strong>{{ $lowStock->pluck('name')->implode(', ') }}</strong>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Main Livewire Table --}}
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6 pb-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Inventory List</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('inventory.forecast') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-bar-chart me-1"></i> Forecast
                </a>
            </div>
        </div>
        <div class="card-body">
            @livewire('dashboard.inventory.index')
        </div>
    </div>
</div>
@endsection