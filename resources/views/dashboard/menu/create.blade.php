@extends('dashboard.layouts.app')

@push('css')
    <!-- Summernote -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
@endpush

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="card shadow-sm">
        <!-- Card Header -->
        <div class="card-header border-1 pt-4 pb-2">
            <div class="card-title">
                <h4 class="mb-0">Add New Menu</h4>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <form action="{{ route('menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Title -->
                <div class="mb-3">
                    <label for="titleInput" class="form-label">Title</label>
                    <input type="text" name="title" id="titleInput" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Menu Category -->
                <div class="mb-3">
                    <label for="menu_category_id" class="form-label">Category</label>
                    <select name="menu_category_id" id="menu_category_id" class="form-select @error('menu_category_id') is-invalid @enderror">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('menu_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('menu_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="summernote" class="form-label">Description</label>
                    <textarea name="description" id="summernote" class="form-control">{{ old('description') }}</textarea>
                    @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label for="imageInput" class="form-label">Upload Image</label>
                    <input type="file" name="image" id="imageInput" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Image Preview -->
                <div class="mb-3">
                    <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid rounded border d-none" style="max-width: 50%;">
                </div>

                <!-- Priority -->
                <div class="mb-3">
                    <label for="priority" class="form-label">Priority</label>
                    <input type="number" name="priority" id="priority" class="form-control @error('priority') is-invalid @enderror" value="{{ old('priority') }}">
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Buttons -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                    <a href="{{ route('menu.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Summernote
            $('#summernote').summernote({
                placeholder: 'Enter Description',
                tabsize: 2,
                height: 150,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Image preview
            $('#imageInput').on('change', function (event) {
                const file = event.target.files[0];
                const preview = $('#imagePreview');

                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.attr('src', e.target.result).removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.addClass('d-none').attr('src', '#');
                }
            });
        });
    </script>
@endpush
