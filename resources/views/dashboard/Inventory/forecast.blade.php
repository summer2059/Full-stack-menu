@extends('dashboard.layouts.app')

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="mb-1">Sales Forecast</h3>
            <span class="text-muted">Predicted demand for <strong>{{ $forecast['day_name'] }}, {{ $forecast['date'] }}</strong></span>
        </div>
        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>

    {{-- Today vs Yesterday --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small mb-1">Today's Revenue</div>
                    <div class="fs-2 fw-bold text-primary">${{ number_format($comparison['today'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small mb-1">Yesterday's Revenue</div>
                    <div class="fs-2 fw-bold text-secondary">${{ number_format($comparison['yesterday'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small mb-1">Change</div>
                    <div class="fs-2 fw-bold {{ $comparison['trend'] === 'up' ? 'text-success' : 'text-danger' }}">
                        {{ $comparison['trend'] === 'up' ? '▲' : '▼' }}
                        {{ abs($comparison['change']) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Forecasted Menu Sales --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-4 pb-2">
                    <h5 class="mb-0">
                        <i class="fa fa-bar-chart text-primary me-2"></i>
                        Predicted Menu Sales Tomorrow
                    </h5>
                </div>
                <div class="card-body p-0">
                    @forelse($forecast['items'] as $item)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $item['menu_title'] }}</div>
                            <span class="badge {{ $item['confidence'] === 'high' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} small">
                                {{ ucfirst($item['confidence']) }} confidence
                            </span>
                        </div>
                        <div class="text-end">
                            <div class="fs-5 fw-bold text-primary">{{ $item['forecasted_qty'] }}</div>
                            <div class="text-muted small">servings</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-line-chart fa-2x mb-3 d-block opacity-25"></i>
                        Not enough historical data yet.<br>
                        <small>Forecast improves after a few days of orders.</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Inventory Needed Tomorrow --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header border-0 pt-4 pb-2">
                    <h5 class="mb-0">
                        <i class="fa fa-archive text-warning me-2"></i>
                        Inventory Needed Tomorrow
                    </h5>
                </div>
                <div class="card-body p-0">
                    @forelse($needed as $row)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $row['item'] }}</div>
                            <div class="text-muted small">
                                Current stock: {{ $row['current_stock'] }} {{ $row['unit'] }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold {{ $row['sufficient'] ? 'text-success' : 'text-danger' }}">
                                {{ number_format($row['needed'], 2) }} {{ $row['unit'] }}
                            </div>
                            @if(!$row['sufficient'])
                                <span class="badge bg-danger-subtle text-danger small">
                                    Short by {{ number_format($row['shortfall'], 2) }} {{ $row['unit'] }}
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success small">Sufficient</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-archive fa-2x mb-3 d-block opacity-25"></i>
                        No inventory linked to menu items yet.<br>
                        <small>Link ingredients to menus to see requirements.</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection