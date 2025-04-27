@extends('layouts.admin')

@section('title', 'Edit Banner')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Banner</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
        <li class="breadcrumb-item active">Edit Banner</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Banner Details
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Subtitle</label>
                            <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="link" class="form-label">Link URL</label>
                            <input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link', $banner->link) }}" placeholder="https://example.com">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: URL where the banner should link to</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $banner->order) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Lower numbers display first</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Uncheck to hide this banner from the site</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="new_images" class="form-label">Banner Images</label>
                            <input type="file" class="form-control @error('new_images.*') is-invalid @enderror" id="new_images" name="new_images[]" accept="image/*" multiple>
                            @error('new_images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Recommended size: 1200×400 pixels, Max 2MB per image. You can select multiple images.</div>
                        </div>
                        
                        <!-- Current Images Section -->
                        <div class="mt-4">
                            <label class="form-label">Current Images</label>
                            <div class="row" id="current-images">
                                @if($banner->images && $banner->images->count() > 0)
                                    @foreach($banner->images as $image)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <img src="{{ $image->image_url }}" alt="Banner Image" class="card-img-top">
                                            <div class="card-body p-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="primary_image_id" 
                                                           id="primary_image_{{ $image->id }}" value="{{ $image->id }}"
                                                           {{ $image->is_primary ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="primary_image_{{ $image->id }}">
                                                        Set as primary
                                                    </label>
                                                </div>
                                                <div class="form-check mt-1">
                                                    <input class="form-check-input" type="checkbox" name="delete_images[]" 
                                                           id="delete_image_{{ $image->id }}" value="{{ $image->id }}">
                                                    <label class="form-check-label text-danger" for="delete_image_{{ $image->id }}">
                                                        Delete
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <div class="alert alert-info">No images uploaded yet.</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview for new images
        const imageInput = document.getElementById('new_images');
        const currentImagesContainer = document.getElementById('current-images');
        
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                // Remove any previous preview elements
                const existingPreviews = document.querySelectorAll('.new-image-preview');
                existingPreviews.forEach(preview => preview.remove());
                
                // Add preview for each selected file
                if (this.files && this.files.length > 0) {
                    const previewRow = document.createElement('div');
                    previewRow.className = 'row mt-3 new-image-preview';
                    
                    const previewTitle = document.createElement('h6');
                    previewTitle.textContent = 'New Images Preview:';
                    previewRow.appendChild(previewTitle);
                    
                    for (let i = 0; i < this.files.length; i++) {
                        const file = this.files[i];
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const previewCol = document.createElement('div');
                            previewCol.className = 'col-md-4 mb-2';
                            
                            const previewCard = document.createElement('div');
                            previewCard.className = 'card';
                            
                            const previewImg = document.createElement('img');
                            previewImg.src = e.target.result;
                            previewImg.className = 'card-img-top';
                            previewImg.alt = 'New Image Preview';
                            
                            const previewCardBody = document.createElement('div');
                            previewCardBody.className = 'card-body p-2';
                            previewCardBody.textContent = file.name;
                            
                            previewCard.appendChild(previewImg);
                            previewCard.appendChild(previewCardBody);
                            previewCol.appendChild(previewCard);
                            previewRow.appendChild(previewCol);
                        }
                        
                        reader.readAsDataURL(file);
                    }
                    
                    currentImagesContainer.parentNode.insertBefore(previewRow, currentImagesContainer.nextSibling);
                }
            });
        }
    });
</script>
@endpush 