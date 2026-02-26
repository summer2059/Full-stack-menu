@extends('dashboard.layouts.app')

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <div class="card-header border-1 pt-6">
                <div class="card-title">
                    <h4>Add New Menu Category</h4>
                </div>
            </div>
            <div class="card-body pt-0 mt-4">
                {{--
                    Component tag: App\Livewire\Dashboard\MenuCategory\Form
                    Livewire converts namespace to kebab-case tag:
                    dashboard.menu-category.form → dashboard-menu-category-form
                --}}
                @livewire('dashboard.menu-category.form')
            </div>
        </div>
    </div>
@endsection