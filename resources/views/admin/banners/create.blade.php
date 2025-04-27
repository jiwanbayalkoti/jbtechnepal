@extends('layouts.admin')

@section('title', 'Add New Banner')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Add New Banner</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
        <li class="breadcrumb-item active">Add New</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
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
            
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Subtitle</label>
                            <input type="text" class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" value="{{ old('subtitle') }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="link" class="form-label">Link URL</label>
                            <input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link') }}" placeholder="https://example.com">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional: URL where the banner should link to</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Lower numbers display first</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Uncheck to hide this banner from the site</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="images" class="form-label">Banner Images <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" 
                                id="images" name="images[]" accept="image/*" multiple required>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Recommended size: 1200×400 pixels, Max 2MB per image. First image will be set as primary.</div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="image-preview-container">
                                <div id="image-previews" class="row"></div>
                                <div class="text-center mt-2" id="no-image-message">
                                    <img src="{{ asset('img/no-image.png') }}" alt="No images selected" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                    <p class="text-muted">No images selected</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imagesInput = document.getElementById('images');
        const imagePreviews = document.getElementById('image-previews');
        const noImageMessage = document.getElementById('no-image-message');
        
        imagesInput.addEventListener('change', function() {
            // Clear existing previews
            imagePreviews.innerHTML = '';
            
            if (this.files.length > 0) {
                noImageMessage.style.display = 'none';
                
                Array.from(this.files).forEach((file, index) => {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'col-md-4 mb-3';
                        
                        const cardDiv = document.createElement('div');
                        cardDiv.className = 'card h-100';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'card-img-top';
                        img.alt = 'Banner preview';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        
                        const cardBody = document.createElement('div');
                        cardBody.className = 'card-body text-center';
                        
                        const caption = document.createElement('p');
                        caption.className = 'card-text';
                        
                        if (index === 0) {
                            caption.innerHTML = '<span class="badge bg-primary">Primary Image</span>';
                        } else {
                            caption.innerHTML = `<span class="badge bg-secondary">Image ${index + 1}</span>`;
                        }
                        
                        cardBody.appendChild(caption);
                        cardDiv.appendChild(img);
                        cardDiv.appendChild(cardBody);
                        previewDiv.appendChild(cardDiv);
                        imagePreviews.appendChild(previewDiv);
                    }
                    
                    reader.readAsDataURL(file);
                });
            } else {
                noImageMessage.style.display = 'block';
            }
        });
    });
</script>
@endpush 