<div>
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form wire:submit.prevent="save">

        {{-- Title --}}
        <div class="col-12 mb-3">
            <label for="title" class="form-label fw-semibold">
                Title <span class="text-danger">*</span>
            </label>
            <input
                type="text"
                id="title"
                wire:model.live="title"
                class="form-control @error('title') is-invalid @enderror"
                placeholder="Enter category title"
            >
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Description --}}
        <div class="col-12 mb-3">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea
                id="description"
                wire:model="description"
                class="form-control @error('description') is-invalid @enderror"
                rows="4"
                placeholder="Enter description (optional)"
            ></textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Image Upload --}}
        <div class="col-12 mb-2">
            <label for="image" class="form-label fw-semibold">
                Upload Image
                @if (!$categoryId)
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input
                type="file"
                id="image"
                wire:model="image"
                class="form-control @error('image') is-invalid @enderror"
                accept="image/*"
            >
            {{-- Upload progress --}}
            <div wire:loading wire:target="image" class="mt-1 text-primary d-flex align-items-center gap-1">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <small>Uploading image...</small>
            </div>
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Image Preview --}}
        <div class="col-12 mb-3">
            @if ($image)
                <div>
                    <small class="text-muted d-block mb-1">New Image Preview:</small>
                    <img
                        src="{{ $image->temporaryUrl() }}"
                        alt="Preview"
                        style="max-width: 200px; height: auto; border-radius: 8px; border: 2px solid #dee2e6;"
                    >
                </div>
            @elseif ($existingImage)
                <div>
                    <small class="text-muted d-block mb-1">Current Image:</small>
                    <img
                        src="{{ asset('uploads/images/' . $existingImage) }}"
                        alt="Current Image"
                        style="max-width: 200px; height: auto; border-radius: 8px; border: 2px solid #dee2e6;"
                    >
                </div>
            @endif
        </div>

        {{-- Priority --}}
        <div class="col-12 mb-3">
            <label for="priority" class="form-label fw-semibold">
                Priority <span class="text-danger">*</span>
            </label>
            <input
                type="number"
                id="priority"
                wire:model.live="priority"
                class="form-control @error('priority') is-invalid @enderror"
                placeholder="Enter priority number"
            >
            @error('priority')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Status --}}
        <div class="col-12 mb-3">
            <label for="status" class="form-label fw-semibold">
                Status <span class="text-danger">*</span>
            </label>
            <select
                id="status"
                wire:model="status"
                class="form-control @error('status') is-invalid @enderror"
            >
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Buttons --}}
        <div class="card-footer text-end mt-4 px-0">
            <button
                type="submit"
                class="btn btn-primary me-3"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                </span>
                {{ $categoryId ? 'Update Category' : 'Add Category' }}
            </button>
            <a href="{{ route('menu-category.index') }}" class="btn btn-light">Cancel</a>
        </div>

    </form>
</div>