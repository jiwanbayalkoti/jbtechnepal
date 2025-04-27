<div id="formContent">
    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $specificationType->name) }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="unit" class="form-label">Unit</label>
        <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $specificationType->unit) }}" placeholder="e.g. GB, inches, GHz">
        <small class="form-text text-muted">Leave empty if no unit is needed</small>
        @error('unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_comparable" name="is_comparable" value="1" {{ old('is_comparable', $specificationType->is_comparable) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_comparable">
                Comparable (show in comparison table)
            </label>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="display_order" class="form-label">Display Order</label>
        <input type="number" class="form-control @error('display_order') is-invalid @enderror" id="display_order" name="display_order" value="{{ old('display_order', $specificationType->display_order) }}" min="0">
        <small class="form-text text-muted">Lower numbers will appear first in the comparison table</small>
        @error('display_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div> 