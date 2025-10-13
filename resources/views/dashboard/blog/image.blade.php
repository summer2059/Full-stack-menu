@extends('dashboard.layouts.app')

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/vendors/photoswipe.css') }}">
    <style>
        .btn-delete {
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 5px;
        }
    </style>
@endpush

@section('content')
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-1 pt-6 d-flex justify-content-between align-items-center">
                <div class="card-title">
                    <h4>Blog Images</h4>
                </div>
                <div>
                    <button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addImageModal">Add
                        More</button>
                    <a href="{{ route('blog.index') }}" class="btn btn-sm btn-light">Go Back</a>
                </div>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body pt-0 mt-4">
                <div class="col-sm-12">
                    <div class="my-gallery card-body row gallery-with-description" itemscope="">
                        @foreach ($image as $data)
                            <figure class="col-xl-3 col-sm-6" itemprop="associatedMedia" itemscope="">
                                <a href="{{ asset('uploads/images') }}/{{ $data->images }}" itemprop="contentUrl"
                                    data-size="1600x950">
                                    <img src="{{ asset('uploads/images') }}/{{ $data->images }}" itemprop="thumbnail"
                                        alt="description">
                                    <div class="caption text-center">
                                        <a href="#" class="btn btn-sm btn-danger btn-delete"
                                            data-image="{{ $data->image }}" data-id="{{ $data->id }}">Delete</a>
                                    </div>
                                </a>
                            </figure>
                        @endforeach
                    </div>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->


    <!-- Add Image Modal -->
    <div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addImageModalLabel">Add More Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addImageForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $id }}">
                        <div class="mb-3">
                            <label for="images" class="form-label">Upload Images</label>
                            <input type="file" class="form-control" name="images[]" id="images" multiple required>
                        </div>
                        <div id="imagePreview" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitImageForm">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('dashboard/assets/js/photoswipe/photoswipe.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/photoswipe/photoswipe-ui-default.min.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/photoswipe/photoswipe.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('submitImageForm').addEventListener('click', function() {
                const formData = new FormData(document.getElementById('addImageForm'));

                fetch('{{ route('blog.uploadImages') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const galleryContainer = document.querySelector(
                                '.gallery-with-description');

                            data.images.forEach(image => {
                                const figure = document.createElement('figure');
                                figure.className = 'col-xl-3 col-sm-6';
                                figure.setAttribute('itemprop', 'associatedMedia');

                                const anchor = document.createElement('a');
                                anchor.href = '{{ asset('uploads/images') }}/' + image;
                                anchor.setAttribute('itemprop', 'contentUrl');
                                anchor.setAttribute('data-size', '1600x950');

                                const img = document.createElement('img');
                                img.src = '{{ asset('uploads/images') }}/' + image;
                                img.setAttribute('itemprop', 'thumbnail');
                                img.alt = 'description';

                                const caption = document.createElement('div');
                                caption.className = 'caption text-center';

                                const deleteButton = document.createElement('a');
                                deleteButton.href = '#';
                                deleteButton.className = 'btn btn-sm btn-danger btn-delete';
                                deleteButton.textContent = 'Delete';

                                caption.appendChild(deleteButton);
                                anchor.appendChild(img);
                                anchor.appendChild(caption);
                                figure.appendChild(anchor);
                                galleryContainer.appendChild(figure);
                            });

                            document.getElementById('addImageForm').reset();
                            document.getElementById('imagePreview').style.display = 'flex';
                            $('#addImageModal').modal('hide');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();

                    const imageName = this.getAttribute('data-image');
                    const productId = this.getAttribute('data-id');
                    if (confirm('Are you sure you want to delete this image?')) {
                        fetch(`{{ url('/dashboard/blog') }}/${productId}/delete-image`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    image: imageName
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove the image from the DOM
                                    this.closest('figure').remove();
                                    alert(data.message);
                                } else {
                                    alert(data.message);
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            });
        });
    </script>
@endpush
