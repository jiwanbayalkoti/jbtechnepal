<div id="formContent">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    
    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $menu->name) }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="url" class="form-label">URL <small class="text-muted">(Auto-generated)</small></label>
            <div class="input-group">
                <span class="input-group-text">/</span>
                <input type="text" class="form-control @error('url') is-invalid @enderror" id="url_display" value="{{ ltrim(old('url', $menu->url), '/') }}" readonly>
                <input type="hidden" id="url" name="url" value="{{ old('url', $menu->url) }}">
            </div>
            <small class="form-text text-muted">URL is auto-generated from the menu name</small>
            @error('url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6">
            <label for="route_name" class="form-label">Route Name <small class="text-muted">(Auto-generated)</small></label>
            <input type="text" class="form-control @error('route_name') is-invalid @enderror" id="route_name_display" value="{{ old('route_name', $menu->route_name) }}" readonly>
            <input type="hidden" id="route_name" name="route_name" value="{{ old('route_name', $menu->route_name) }}">
            <small class="form-text text-muted">Route name is auto-generated from the menu name</small>
            @error('route_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="mb-3">
        <label for="icon" class="form-label">Icon Class (FontAwesome)</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-icons"></i></span>
            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $menu->icon) }}" placeholder="e.g. fas fa-home">
            @error('icon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="form-text text-muted">Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> to find icons</small>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
            <select class="form-select @error('location') is-invalid @enderror" id="location" name="location" required>
                @foreach($locations as $location)
                    <option value="{{ $location }}" {{ old('location', $menu->location) == $location ? 'selected' : '' }}>
                        {{ ucfirst($location) }}
                    </option>
                @endforeach
            </select>
            @error('location')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-4">
            <label for="order" class="form-label">Order <span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $menu->order) }}" min="0" required>
            @error('order')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-4">
            <label for="parent_id" class="form-label">Parent Menu Item</label>
            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                <option value="">None (Top Level)</option>
                @foreach($menuItems as $item)
                    <option value="{{ $item->id }}" data-url="{{ $item->url }}" {{ old('parent_id', $menu->parent_id) == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="category_id" class="form-label">Associated Category</label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
            <option value="">None</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" data-slug="{{ $category->slug }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Link this menu item to a category (optional)</small>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3" id="brand_section" style="display: none;">
        <label for="brand_for_url" class="form-label">Brand for URL</label>
        <select class="form-select" id="brand_for_url" name="brand_for_url">
            <option value="all">All Brands</option>
            @foreach($brands as $brand)
                <?php
                // Extract brand value from URL if it exists
                $currentBrandValue = '';
                if (preg_match('/\/([^\/]+)-by-brand\/([^\/]+)/', $menu->url, $matches)) {
                    $currentBrandValue = $matches[2];
                }
                ?>
                <option value="{{ $brand->slug }}" {{ old('brand_for_url', $currentBrandValue) == $brand->slug ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Select a brand to use in the URL format: category-by-brand/brand</small>
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', $menu->active) ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Active</label>
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="is_dynamic_page" name="is_dynamic_page" value="1" {{ old('is_dynamic_page', $menu->is_dynamic_page) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_dynamic_page">Is Dynamic Page</label>
        <small class="form-text text-muted d-block">Check this if you want to create a custom page with content</small>
    </div>
    
    <div id="dynamic-page-fields" class="mb-3" style="display: none;">
        <div class="mb-3">
            <label for="slug" class="form-label">Page Slug</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $menu->slug) }}">
            <small class="form-text text-muted">URL-friendly name for the page. Leave blank to generate from page name.</small>
            @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="content" class="form-label">Page Content</label>
            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10">{{ old('content', $menu->content) }}</textarea>
            @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Hidden input to store the menu ID -->
    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
</div>

<script>
// Execute immediately to ensure it runs right away
(function() {
    // Show/hide dynamic page fields
    const isDynamicCheckbox = document.getElementById('is_dynamic_page');
    const dynamicFields = document.getElementById('dynamic-page-fields');
    const urlSection = document.getElementById('url').parentNode.parentNode.parentNode;
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const urlInput = document.getElementById('url');
    const urlDisplayInput = document.getElementById('url_display');
    const routeNameInput = document.getElementById('route_name');
    const routeNameDisplayInput = document.getElementById('route_name_display');
    const categorySelect = document.getElementById('category_id');
    const brandSection = document.getElementById('brand_section');
    const brandSelect = document.getElementById('brand_for_url');
    const parentSelect = document.getElementById('parent_id');
    
    // Store parent menu data
    const parentMenus = {};
    
    // Initialize parent menu data
    if (parentSelect) {
        Array.from(parentSelect.options).forEach(option => {
            if (option.value) {
                const parentName = option.textContent.trim();
                const url = option.getAttribute('data-url') || '';
                
                // Try to extract category and brand from parent URL
                const match = url.match(/\/([^\/]+)-by-brand\/([^\/]+)/);
                
                if (match) {
                    parentMenus[option.value] = {
                        categorySlug: match[1],
                        brand: match[2],
                        url: url,
                        name: parentName
                    };
                } else {
                    // Store the parent's name even if it doesn't have a category-by-brand URL
                    parentMenus[option.value] = {
                        name: parentName,
                        url: url
                    };
                }
            }
        });
    }
    
    function toggleDynamicFields() {
        if (isDynamicCheckbox && isDynamicCheckbox.checked) {
            dynamicFields.style.display = 'block';
            urlSection.style.display = 'none';
            brandSection.style.display = 'none';
        } else {
            dynamicFields.style.display = 'none';
            urlSection.style.display = 'flex';
            
            // Always show brand section when not a dynamic page
            brandSection.style.display = 'block';
        }
    }
    
    // Set initial state immediately
    toggleDynamicFields();
    
    // Add event listener
    if (isDynamicCheckbox) {
        isDynamicCheckbox.addEventListener('change', toggleDynamicFields);
    }
    
    // Parent select change handler
    if (parentSelect) {
        parentSelect.addEventListener('change', function() {
            toggleDynamicFields();
            updateUrls();
        });
    }
    
    // Category select change handler
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            updateUrls();
        });
    }
    
    // Brand select change handler
    if (brandSelect) {
        brandSelect.addEventListener('change', updateUrls);
    }
    
    function generateURLSafeString(name) {
        return name
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }
    
    function updateUrls() {
        const name = nameInput.value;
        const slug = generateURLSafeString(name);
        
        // For dynamic pages
        if (isDynamicCheckbox && isDynamicCheckbox.checked) {
            if (slugInput && slugInput.value === '') {
                slugInput.value = slug;
            }
        } 
        // For regular menu items
        else {
            // Check for parent menu first
            const parentId = parentSelect ? parentSelect.value : '';
            
            if (parentId) {
                const parentInfo = parentMenus[parentId];
                const brandValue = brandSelect.value;
                
                let categorySlug;
                
                // If parent already has a category-by-brand format, use its category
                if (parentInfo.categorySlug) {
                    categorySlug = parentInfo.categorySlug;
                }
                // Otherwise use parent's name as the category
                else {
                    categorySlug = generateURLSafeString(parentInfo.name);
                }
                
                // Format based on parent: category-by-brand/brand
                const newUrl = '/' + categorySlug + '-by-brand/' + brandValue;
                urlInput.value = newUrl;
                urlDisplayInput.value = categorySlug + '-by-brand/' + brandValue;
                
                // Format route name
                routeNameInput.value = 'products.by.brand';
                routeNameDisplayInput.value = 'products.by.brand';
            }
            // Check if category is selected (only if no parent)
            else if (categorySelect && categorySelect.value) {
                // Get selected option
                const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                // Get category slug
                const categorySlug = selectedOption.getAttribute('data-slug');
                // Get brand
                const brandValue = brandSelect.value;
                
                // Format: category-by-brand/brand
                const newUrl = '/' + categorySlug + '-by-brand/' + brandValue;
                urlInput.value = newUrl;
                urlDisplayInput.value = categorySlug + '-by-brand/' + brandValue;
                
                // Format route name
                routeNameInput.value = 'products.by.brand';
                routeNameDisplayInput.value = 'products.by.brand';
            } else {
                // Default behavior if no parent or category selected
                const newUrl = '/' + slug;
                urlInput.value = newUrl;
                urlDisplayInput.value = slug;
                
                // Set route name input values
                routeNameInput.value = slug;
                routeNameDisplayInput.value = slug;
            }
        }
    }
    
    if (nameInput) {
        // Update immediately on load
        updateUrls();
        
        // Use both 'input' (live typing) and 'blur' events
        nameInput.addEventListener('input', updateUrls);
        nameInput.addEventListener('blur', updateUrls);
        nameInput.addEventListener('keyup', updateUrls);
    }
    
    // Wait for TinyMCE to be available
    function initTinyMCE() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#content',
                height: 400,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                menubar: 'file edit view insert format tools table help'
            });
        }
    }
    
    // Initialize TinyMCE
    initTinyMCE();
})();

// Also run when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Re-trigger the updateUrls function to ensure everything is properly set
    if (document.getElementById('name')) {
        const event = new Event('input');
        document.getElementById('name').dispatchEvent(event);
    }
});
</script> 