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
            {{-- Component: App\Livewire\Dashboard\Inventory\Form --}}
            @livewire('dashboard.inventory.form')
        </div>
    </div>

    {{-- How Inventory Works info card --}}
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