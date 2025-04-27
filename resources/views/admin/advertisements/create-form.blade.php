<!-- Create Advertisement Form -->
<div class="form-group">
    <label for="title">Title <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="title" name="title" required>
</div>

<div class="form-group">
    <label for="url">URL <span class="text-danger">*</span></label>
    <input type="url" class="form-control" id="url" name="url" required>
</div>

<div class="form-group">
    <label for="content">Content/Description</label>
    <textarea class="form-control" id="content" name="content" rows="3"></textarea>
</div>

<div class="form-group">
    <label for="image">Image <span class="text-danger">*</span></label>
    <div class="input-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="image" name="image" required>
            <label class="custom-file-label" for="image">Choose image...</label>
        </div>
    </div>
    <small class="form-text text-muted">Recommended size: 800x400px (max 2MB)</small>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="position">Position <span class="text-danger">*</span></label>
            <select class="form-control" id="position" name="position" required>
                <option value="">Select Position</option>
                <option value="homepage_top">Homepage Top</option>
                <option value="homepage_sidebar">Homepage Sidebar</option>
                <option value="category_page">Category Page</option>
                <option value="product_page">Product Page</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" class="form-control" id="display_order" name="display_order" min="1" value="1">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="start_date">Start Date <span class="text-danger">*</span></label>
            <input type="text" class="form-control datepicker" id="start_date" name="start_date" value="{{ date('Y-m-d') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="text" class="form-control datepicker" id="end_date" name="end_date">
            <small class="form-text text-muted">Leave empty for indefinite duration</small>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
        <label class="custom-control-label" for="is_active">Active</label>
    </div>
</div> 