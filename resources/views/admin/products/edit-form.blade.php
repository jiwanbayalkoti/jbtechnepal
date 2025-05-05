<div id="formContent">
<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="subcategory_id" class="form-label">Subcategory</label>
        <select class="form-select @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id">
            <option value="">Select Subcategory</option>
            @foreach($subcategories as $subcategory)
                <option value="{{ $subcategory->id }}" {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                    {{ $subcategory->name }}
                </option>
            @endforeach
        </select>
        @error('subcategory_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="brand" class="form-label">Brand</label>
            <select class="form-select @error('brand') is-invalid @enderror" id="brand" name="brand">
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->name }}" {{ old('brand', $product->brand) == $brand->name ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            @error('brand')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6">
            <label for="model" class="form-label">Model</label>
            <select class="form-select @error('model') is-invalid @enderror" id="model" name="model">
                <option value="">Select Model</option>
                @foreach(\App\Models\Model::orderBy('name')->get() as $modelOption)
                    <option value="{{ $modelOption->name }}" {{ old('model', $product->model) == $modelOption->name ? 'selected' : '' }}>
                        {{ $modelOption->name }}
                    </option>
                @endforeach
            </select>
            @error('model')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="mb-3">
        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Current Images</label>
        <div id="currentImages" class="row g-2 mb-2">
            @if($product->images && count($product->images) > 0)
                @foreach($product->images as $image)
                    <div class="col-md-3 image-preview-container">
                        <div class="position-relative">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }}" class="img-thumbnail" style="height: 150px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-image" data-image-id="{{ $image->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted">No images uploaded</p>
            @endif
        </div>
        
        <label for="images" class="form-label">Add More Images</label>
        <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
        <small class="form-text text-muted">Upload product images (JPEG, PNG, GIF, max 2MB each). You can select multiple files.</small>
        @error('images')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        
        <div id="imagePreview" class="row g-2 mt-2"></div>
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    @if($specificationTypes && $specificationTypes->count() > 0)
    <hr>
    <h5>Specifications</h5>
    
    @foreach($specificationTypes as $specType)
    <div class="mb-3">
        <label for="specifications_{{ $specType->id }}" class="form-label">{{ $specType->name }}</label>
        <input type="text" class="form-control" 
               id="specifications_{{ $specType->id }}" 
               name="specifications[{{ $specType->id }}]" 
               value="{{ old('specifications.'.$specType->id, $specValues[$specType->id] ?? '') }}"
               placeholder="{{ $specType->description }}">
    </div>
    @endforeach
    @endif
</form>
</div>

<script>
// This script will run when this form content is loaded into the modal
document.addEventListener('DOMContentLoaded', function() {
    // Find the category select in this specific form
    const formContent = document.getElementById('formContent');
    if (!formContent) return;
    
    const categorySelect = formContent.querySelector('select[name="category_id"]');
    const subcategorySelect = formContent.querySelector('select[name="subcategory_id"]');
    
    if (categorySelect && subcategorySelect) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            
            // Clear existing options except the first one
            while (subcategorySelect.options.length > 1) {
                subcategorySelect.remove(1);
            }
            
            // Also clear model dropdown when category changes
            const modelSelect = formContent.querySelector('select[name="model"]');
            if (modelSelect) {
                while (modelSelect.options.length > 1) {
                    modelSelect.remove(1);
                }
            }
            
            if (categoryId) {
                // Fetch subcategories for the selected category
                fetch(`{{ url('admin/subcategories') }}/${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.subcategories) {
                            // Add new options
                            data.subcategories.forEach(subcategory => {
                                const option = document.createElement('option');
                                option.value = subcategory.id;
                                option.textContent = subcategory.name;
                                subcategorySelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching subcategories:', error);
                    });
            }
        });
        
        // Load models based on subcategory change
        subcategorySelect.addEventListener('change', function() {
            const subcategoryId = this.value;
            const modelSelect = formContent.querySelector('select[name="model"]');
            
            if (modelSelect) {
                // Clear existing model options except the first one
                while (modelSelect.options.length > 1) {
                    modelSelect.remove(1);
                }
                
                if (subcategoryId) {
                    // Fetch models for the selected subcategory
                    fetch(`{{ url('admin/models-by-subcategory') }}/${subcategoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.models) {
                                // Add new options
                                data.models.forEach(model => {
                                    const option = document.createElement('option');
                                    option.value = model.name;
                                    option.textContent = model.name;
                                    modelSelect.appendChild(option);
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching models:', error);
                        });
                }
            }
        });
        
        console.log('Category change event bound in edit form');
    }

    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    const currentImages = document.getElementById('currentImages');
    
    // Handle new image selection
    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = '';
        
        for (const file of this.files) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                const col = document.createElement('div');
                col.className = 'col-md-3 image-preview-container';
                
                reader.onload = function(e) {
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" style="height: 150px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-preview">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                };
                
                reader.readAsDataURL(file);
                imagePreview.appendChild(col);
            }
        }
    });
    
    // Handle preview image removal
    imagePreview.addEventListener('click', function(e) {
        if (e.target.closest('.remove-preview')) {
            e.target.closest('.image-preview-container').remove();
        }
    });
    
    // Handle current image deletion
    currentImages.addEventListener('click', function(e) {
        if (e.target.closest('.delete-image')) {
            const button = e.target.closest('.delete-image');
            const imageId = button.dataset.imageId;
            const container = button.closest('.image-preview-container');
            
            if (confirm('Are you sure you want to delete this image?')) {
                // Add a hidden input to mark this image for deletion
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_images[]';
                deleteInput.value = imageId;
                document.querySelector('form').appendChild(deleteInput);
                
                // Remove the image container
                container.remove();
            }
        }
    });

    // Add event listeners for delete image buttons
    document.querySelectorAll('.delete-image').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-image-id');
            const container = this.closest('.image-preview-container');
            
            // Create a hidden input to mark this image for deletion
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'delete_images[]';
            hiddenInput.value = imageId;
            formContent.querySelector('form').appendChild(hiddenInput);
            
            // Hide the image container
            container.style.display = 'none';
        });
    });
});
</script> 