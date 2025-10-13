@extends('dashboard.layouts.app')

@push('css')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<!-- Include Summernote CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">

<style>
    #imagePreview, #imagePreview1, #imagePreview2 {
        max-width: 50%;
        width: 400px;
        height: 400px;
        border: 1px solid #ddd;
        padding: 5px;
        background-color: #f8f8f8;
        display: none;
        margin-bottom: 10px; /* Added margin for spacing */
    }
</style>
@endpush

@section('content')
<div id="kt_app_content_container" class="app-container container-xxl">
    <div class="card">
        <div class="card-header border-1 pt-6">
            <div class="card-title">
                <h4>Add New Blog</h4>
            </div>
        </div>
        <div class="card-body pt-0 mt-4">
            <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control" >
                    @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                </div>

                <div class="form-group">
                    <label for="summernote">Description</label>
                    <textarea name="description" class="form-control" id="summernote"></textarea>
                </div>

                <div class="form-group">
                    <label for="images">Blog Images</label>
                    <input type="file" class="form-control" name="images[]" id="images" multiple> <!-- Added onchange event for preview -->
                </div>

                <!-- Image Previews -->

                <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                <div class="card-footer text-end">
                    <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-primary me-3" type="submit">Submit</button>
                        <a href="{{ route('blog.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#summernote').summernote({
            placeholder: 'Enter Description',
            tabsize: 2,
            height: 120,
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
    });


</script>
@endpush
