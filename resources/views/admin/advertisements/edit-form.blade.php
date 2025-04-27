<!-- Edit Advertisement Form -->
<div class="form-group">
    <label for="title">Title <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="title" name="title" value="{{ $advertisement->title ?? '' }}" required>
</div>

<div class="form-group">
    <label for="url">URL <span class="text-danger">*</span></label>
    <input type="url" class="form-control" id="url" name="url" value="{{ $advertisement->url ?? '' }}" required>
</div>

<div class="form-group">
    <label for="content">Content/Description</label>
    <textarea class="form-control" id="content" name="content" rows="3">{{ $advertisement->content ?? '' }}</textarea>
</div>

<div class="form-group">
    <label for="image">Image</label>
    <div class="input-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="image" name="image">
            <label class="custom-file-label" for="image">Choose new image...</label>
        </div>
    </div>
    <small class="form-text text-muted">Recommended size: 800x400px (max 2MB). Leave empty to keep current image.</small>
    
    @if(!empty($advertisement->image))
    <div class="mt-2">
        <label>Current Image:</label>
        <div class="mt-1">
            <img src="{{ asset('storage/' . $advertisement->image) }}" class="img-thumbnail" style="max-height: 150px;">
            <div class="mt-1">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="remove_image" name="remove_image" value="1">
                    <label class="custom-control-label" for="remove_image">Remove current image</label>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="position">Position <span class="text-danger">*</span></label>
            <select class="form-control" id="position" name="position" required>
                <option value="">Select Position</option>
                <option value="homepage_top" {{ (isset($advertisement) && $advertisement->position == 'homepage_top') ? 'selected' : '' }}>Homepage Top</option>
                <option value="homepage_sidebar" {{ (isset($advertisement) && $advertisement->position == 'homepage_sidebar') ? 'selected' : '' }}>Homepage Sidebar</option>
                <option value="category_page" {{ (isset($advertisement) && $advertisement->position == 'category_page') ? 'selected' : '' }}>Category Page</option>
                <option value="product_page" {{ (isset($advertisement) && $advertisement->position == 'product_page') ? 'selected' : '' }}>Product Page</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" class="form-control" id="display_order" name="display_order" min="1" value="{{ $advertisement->display_order ?? 1 }}">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="start_date">Start Date <span class="text-danger">*</span></label>
            <input type="text" class="form-control datepicker" id="start_date" name="start_date" value="{{ isset($advertisement->start_date) ? date('Y-m-d', strtotime($advertisement->start_date)) : '' }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="text" class="form-control datepicker" id="end_date" name="end_date" value="{{ isset($advertisement->end_date) ? date('Y-m-d', strtotime($advertisement->end_date)) : '' }}">
            <small class="form-text text-muted">Leave empty for indefinite duration</small>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ (isset($advertisement) && $advertisement->is_active) ? 'checked' : '' }}>
        <label class="custom-control-label" for="is_active">Active</label>
    </div>
</div>

@if(isset($advertisement) && ($advertisement->views > 0 || $advertisement->clicks > 0))
<div class="form-group">
    <label>Statistics</label>
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-light mb-0">
                <div class="card-body py-2 text-center">
                    <div class="h4 mb-0">{{ number_format($advertisement->views) }}</div>
                    <div class="small text-muted">Views</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light mb-0">
                <div class="card-body py-2 text-center">
                    <div class="h4 mb-0">{{ number_format($advertisement->clicks) }}</div>
                    <div class="small text-muted">Clicks</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light mb-0">
                <div class="card-body py-2 text-center">
                    <div class="h4 mb-0">{{ $advertisement->views > 0 ? number_format(($advertisement->clicks / $advertisement->views) * 100, 2) : '0.00' }}%</div>
                    <div class="small text-muted">CTR</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif 