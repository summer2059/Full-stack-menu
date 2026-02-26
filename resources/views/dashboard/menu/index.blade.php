@extends('dashboard.layouts.app')

@push('css')
<style>
    nav[role="navigation"] .pagination { margin-bottom: 0; }
    nav[role="navigation"] .pagination .page-link {
        border-radius: 6px;
        margin: 0 2px;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <div class="card-header border-1 pt-6">
                <div class="card-title">
                    <h4>Menu List</h4>
                </div>
            </div>
            <div class="card-body">
                @livewire('dashboard.menu.index')
            </div>
        </div>
    </div>
@endsection