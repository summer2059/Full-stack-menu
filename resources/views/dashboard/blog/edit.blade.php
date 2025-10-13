@extends('dashboard.layouts.app')

@push('css')

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
 <!-- Include Summernote CSS -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">

<style>
    #imagePreview, #imagePreview2 {
        max-width: 50%;
        width: 400px;
        height: 400px;
        border: 1px solid #ddd;
        padding: 5px;
        background-color: #f8f8f8;
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <div class="card-header border-1 pt-6">
                <div class="card-title">
                    <h4>Edit Blog</h4>
                </div>
            </div>
            <div class="card-body pt-0 mt-4">
                <form action="{{ route('blog.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="col-12">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="title" value="{{ old('title', $data->title) }}" >
                            <label for="title">Title</label>
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating mb-3">
                        <textarea name="description" class="form-control" id="summernote">{{ $data->description }}</textarea>
                        <label for="summernote">Description</label>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <select name="status" id="status" class="form-select" >
                            <option value="1" {{ old('status', $data->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $data->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    
                    
                    <div class="card-footer text-end">
                        <div class="col-sm-9 offset-sm-3">
                            <button class="btn btn-primary me-3" type="submit">update</button>
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
    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById(previewId);
            output.style.backgroundImage = "url('" + reader.result + "')";
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush
