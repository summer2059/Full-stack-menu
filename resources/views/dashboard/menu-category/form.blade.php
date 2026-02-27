@extends('dashboard.layouts.app')

@push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endpush

@php
    $isEdit  = isset($sc);
    $title   = $isEdit ? 'Edit Menu Category' : 'Add New Menu Category';
    $action  = $isEdit ? route('menu-category.update', $sc->id) : route('menu-category.store');
    $icon    = $isEdit ? 'fa-pencil-square-o' : 'fa-plus-circle';
    $btnText = $isEdit ? 'Update Category' : 'Save Category';
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
                <a href="{{ route('menu-category.index') }}" class="btn btn-sm btn-secondary">
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
                        value="{{ old('title', $sc->title ?? '') }}"
                        placeholder="e.g. Food, Drinks, Desserts">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" id="summernote"
                        class="form-control">{{ old('description', $sc->description ?? '') }}</textarea>
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
                    @if($isEdit && !empty($sc->image))
                        <img id="imagePreview"
                            src="{{ asset('uploads/images/' . $sc->image) }}"
                            class="img-fluid rounded border"
                            style="max-width:200px;">
                    @else
                        <img id="imagePreview" src="#"
                            class="img-fluid rounded border d-none"
                            style="max-width:200px;">
                    @endif
                </div>

                {{-- Priority --}}
                <div class="mb-3">
                    <label for="priority" class="form-label fw-semibold">Priority</label>
                    <input type="number" name="priority" id="priority"
                        class="form-control @error('priority') is-invalid @enderror"
                        value="{{ old('priority', $sc->priority ?? '') }}"
                        placeholder="e.g. 1">
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select name="status" id="status"
                        class="form-select @error('status') is-invalid @enderror">
                        <option value="1" {{ old('status', $sc->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $sc->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fa fa-floppy-o me-1"></i> {{ $btnText }}
                    </button>
                    <a href="{{ route('menu-category.index') }}" class="btn btn-secondary">Cancel</a>
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
        tabsize: 2, height: 120,
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
        } else {
            $('#imagePreview').addClass('d-none').attr('src', '#');
        }
    });
});
</script>
@endpush