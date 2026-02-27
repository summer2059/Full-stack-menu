@extends('dashboard.layouts.app')

@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endpush

@php
    $isEdit  = isset($data);
    $title   = $isEdit ? 'Edit Menu' : 'Add New Menu';
    $action  = $isEdit ? route('menu.update', $data->id) : route('menu.store');
    $icon    = $isEdit ? 'fa-pencil-square-o' : 'fa-plus-circle';
    $btnText = $isEdit ? 'Update Menu' : 'Save Menu';
@endphp

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="card shadow-sm">

        <div class="card-header border-1 pt-4 pb-2">
            <div class="card-title">
                <h4 class="mb-0">
                    <i class="fa {{ $icon }} text-primary me-2"></i>{{ $title }}
                </h4>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('menu.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($isEdit) @method('PUT') @endif

                {{-- Title --}}
                <div class="mb-3">
                    <label for="titleInput" class="form-label fw-semibold">
                        Title <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title" id="titleInput"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $data->title ?? '') }}"
                        placeholder="e.g. Cheese Burger">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label for="menu_category_id" class="form-label fw-semibold">
                        Category <span class="text-danger">*</span>
                    </label>
                    <select name="menu_category_id" id="menu_category_id"
                        class="form-select @error('menu_category_id') is-invalid @enderror">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('menu_category_id', $data->menu_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('menu_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="summernote" class="form-label fw-semibold">Description</label>
                    <textarea name="description" id="summernote"
                        class="form-control">{{ old('description', $data->description ?? '') }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Image --}}
                <div class="mb-3">
                    <label for="imageInput" class="form-label fw-semibold">
                        {{ $isEdit ? 'Update Image' : 'Upload Image' }}
                        @if($isEdit)
                            <span class="text-muted small fw-normal">(leave blank to keep current)</span>
                        @endif
                    </label>
                    <input type="file" name="image" id="imageInput"
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Image Preview --}}
                <div class="mb-3">
                    @if($isEdit && !empty($data->image))
                        <img id="imagePreview"
                            src="{{ asset('uploads/images/' . $data->image) }}"
                            class="img-fluid rounded border"
                            style="max-width:220px;">
                    @else
                        <img id="imagePreview" src="#"
                            class="img-fluid rounded border d-none"
                            style="max-width:220px;">
                    @endif
                </div>

                <div class="row">
                    {{-- Price --}}
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label fw-semibold">Price ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="price" id="price"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $data->price ?? '') }}"
                                step="0.01" min="0" placeholder="0.00">
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Rating --}}
                    <div class="col-md-4 mb-3">
                        <label for="rating" class="form-label fw-semibold">Rating (1–5)</label>
                        <input type="number" name="rating" id="rating"
                            class="form-control @error('rating') is-invalid @enderror"
                            value="{{ old('rating', $data->rating ?? '') }}"
                            step="0.1" min="1" max="5" placeholder="e.g. 4.5">
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Priority --}}
                    <div class="col-md-4 mb-3">
                        <label for="priority" class="form-label fw-semibold">Priority</label>
                        <input type="number" name="priority" id="priority"
                            class="form-control @error('priority') is-invalid @enderror"
                            value="{{ old('priority', $data->priority ?? '') }}"
                            placeholder="e.g. 1">
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select name="status" id="status"
                        class="form-select @error('status') is-invalid @enderror">
                        <option value="1" {{ old('status', $data->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $data->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fa fa-floppy-o me-1"></i> {{ $btnText }}
                    </button>
                    <a href="{{ route('menu.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script>
$(document).ready(function () {
    $('#summernote').summernote({
        placeholder: 'Enter description...',
        tabsize: 2, height: 150,
        toolbar: [
            ['style', ['style']],
            ['font',  ['bold','underline','italic','clear']],
            ['color', ['color']],
            ['para',  ['ul','ol','paragraph']],
            ['table', ['table']],
            ['insert',['link','picture','video']],
            ['view',  ['fullscreen','codeview','help']]
        ]
    });

    $('#imageInput').on('change', function (e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => $('#imagePreview').attr('src', e.target.result).removeClass('d-none');
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush