@extends('dashboard.layouts.app')

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <div class="card-header border-1 pt-6">
                <div class="card-title">
                    <h4>Edit Menu Category</h4>
                </div>
            </div>
            <div class="card-body pt-0 mt-4">
                {{--
                    Component tag: App\Livewire\Dashboard\MenuCategory\Form
                    Pass the category id for edit mode via mount()
                --}}
                @livewire('dashboard.menu-category.form', ['id' => $category->id])
            </div>
        </div>
    </div>
@endsection