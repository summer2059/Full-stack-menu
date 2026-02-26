@extends('dashboard.layouts.app')

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card shadow-sm">
            <div class="card-header border-1 pt-4 pb-2">
                <div class="card-title">
                    <h4 class="mb-0">Edit Menu</h4>
                </div>
            </div>
            <div class="card-body">
                @livewire('dashboard.menu.form', ['id' => $menu->id])
            </div>
        </div>
    </div>
@endsection