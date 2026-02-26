<div>
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

        <div class="mb-3">
            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
            <input type="text" wire:model.live="title" class="form-control @error('title') is-invalid @enderror" placeholder="Enter menu title" >
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
            <select wire:model.live="menu_category_id" class="form-select @error('menu_category_id') is-invalid @enderror" >
                <option value="">-- Select Category --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                @endforeach
            </select>
            @error('menu_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter description (optional)" ></textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-2">
            <label class="form-label fw-semibold">
                Upload Image
                @if (!$menuId) <span class="text-danger">*</span> @endif
            </label>
            <input type="file" wire:model="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" >
            {{-- <div wire:loading wire:target="image" class="mt-1 text-primary d-flex align-items-center gap-1">
                <div class="spinner-border spinner-border-sm" role="status"></div>
                <small>Uploading image...</small>
            </div> --}}
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            @if ($image)
                <div>
                    <small class="text-muted d-block mb-1">New Image Preview:</small>
                    <img src="{{ $image->temporaryUrl() }}" alt="Preview" style="max-width: 200px; height: auto; border-radius: 8px; border: 2px solid #dee2e6;" >
                </div>
            @elseif ($existingImage)
                <div>
                    <small class="text-muted d-block mb-1">Current Image:</small>
                    <img src="{{ asset('uploads/images/' . $existingImage) }}" alt="Current Image" style="max-width: 200px; height: auto; border-radius: 8px; border: 2px solid #dee2e6;" >
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Price ($) <span class="text-danger">*</span></label>
            <input type="number" wire:model.live="price" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" placeholder="0.00" >
            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Rating (1–5) <span class="text-danger">*</span></label>
            <input type="number" wire:model.live="rating" step="0.1" min="1" max="5" class="form-control @error('rating') is-invalid @enderror" placeholder="e.g. 4.5" >
            @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
            <input type="number" wire:model.live="priority" class="form-control @error('priority') is-invalid @enderror" placeholder="Enter priority number" >
            @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
            <select wire:model="status" class="form-select @error('status') is-invalid @enderror" >
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary me-2" wire:loading.attr="disabled" wire:target="save" >
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                </span>
                {{ $menuId ? 'Update Menu' : 'Add Menu' }}
            </button>
            <a href="{{ route('menu.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

