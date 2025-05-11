<form id="editMenuForm" method="POST" action="{{ route('admin.menus.update', ['menuItem' => $menu->id]) }}">
    @csrf
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
    
    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $menu->name) }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="url" class="form-label">URL</label>
            <input type="text" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $menu->url) }}">
            <small class="form-text text-muted">External or internal URL (e.g., /contact, https://example.com)</small>
            @error('url')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="col-md-6">
            <label for="route_name" class="form-label">Route Name</label>
            <input type="text" class="form-control @error('route_name') is-invalid @enderror" id="route_name" name="route_name" value="{{ old('route_name', $menu->route_name) }}">
            <small class="form-text text-muted">Laravel route name (e.g., home, contact.index)</small>
            @error('route_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text text-danger mt-2">Either URL or Route Name must be provided.</div>
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
                <option value="main" {{ old('location', $menu->location) == 'main' ? 'selected' : '' }}>Main Menu</option>
                <option value="footer" {{ old('location', $menu->location) == 'footer' ? 'selected' : '' }}>Footer Menu</option>
                <option value="admin" {{ old('location', $menu->location) == 'admin' ? 'selected' : '' }}>Admin Menu</option>
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
                    <option value="{{ $item['id'] }}" {{ old('parent_id', $menu->parent_id) == $item['id'] ? 'selected' : '' }}>
                        {{ $item['display_name'] }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', $menu->active) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Active</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="is_mega_menu" name="is_mega_menu" value="1" {{ old('is_mega_menu', $menu->is_mega_menu) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_mega_menu">Mega Menu</label>
            </div>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="category_id" class="form-label">Related Category</label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
            <option value="">None</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3" id="brand_section">
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
    
    <div class="mb-3 form-check" id="auto_generate_section">
        <input type="checkbox" class="form-check-input" id="auto_generate_models" name="auto_generate_models" value="1" 
        {{ old('auto_generate_models', $menu->auto_generate_models) ? 'checked' : '' }}>
        <label class="form-check-label" for="auto_generate_models">Auto-generate model submenus</label>
        <small class="form-text text-muted d-block">Automatically create child menu items for all models associated with this brand and category</small>
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="is_dynamic_page" name="is_dynamic_page" value="1" {{ old('is_dynamic_page', $menu->is_dynamic_page) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_dynamic_page">Is Dynamic Page</label>
        <small class="form-text text-muted d-block">Check this if you want to create a custom page with content</small>
    </div>
    
    <div id="dynamic-page-fields" class="mb-3" {{ !old('is_dynamic_page', $menu->is_dynamic_page) ? 'style="display: none;"' : '' }}>
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
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle dynamic page fields based on checkbox
    const isDynamicCheckbox = document.getElementById('is_dynamic_page');
    const dynamicFields = document.getElementById('dynamic-page-fields');
    const brandSection = document.getElementById('brand_section');
    const autoGenerateSection = document.getElementById('auto_generate_section');
    
    if (isDynamicCheckbox && dynamicFields) {
        isDynamicCheckbox.addEventListener('change', function() {
            dynamicFields.style.display = this.checked ? 'block' : 'none';
            brandSection.style.display = this.checked ? 'none' : 'block';
            autoGenerateSection.style.display = this.checked ? 'none' : 'block';
        });
        
        // Initialize visibility on load
        if (isDynamicCheckbox.checked) {
            dynamicFields.style.display = 'block';
            brandSection.style.display = 'none';
            autoGenerateSection.style.display = 'none';
        } else {
            dynamicFields.style.display = 'none';
            brandSection.style.display = 'block';
            autoGenerateSection.style.display = 'block';
        }
    }
    
    // Handle parent menu selection affecting location
    const parentSelect = document.getElementById('parent_id');
    if (parentSelect) {
        parentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const locationText = selectedOption.textContent.match(/\((.*?)\)/);
                if (locationText && locationText[1]) {
                    const location = locationText[1];
                    const locationSelect = document.getElementById('location');
                    if (locationSelect) {
                        locationSelect.value = location;
                    }
                }
            }
        });
    }
    
    // Initialize TinyMCE if it exists
    if (typeof tinymce !== 'undefined' && document.getElementById('content')) {
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
});
</script> 