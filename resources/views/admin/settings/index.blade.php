@extends('layouts.admin')

@section('title', 'System Settings')

@section('styles')
<style>
    .settings-header {
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }
    
    .settings-header .title {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    
    .settings-header .description {
        color: #6c757d;
    }
    
    .settings-card {
        height: 100%;
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .settings-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .settings-card .card-header {
        padding: 1rem;
        border-bottom: 0;
    }
    
    .settings-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }
    
    .settings-icon.general {
        background-color: rgba(255,255,255,0.2);
    }
    
    .settings-icon.seo {
        background-color: rgba(255,255,255,0.2);
    }
    
    .settings-icon.contact {
        background-color: rgba(255,255,255,0.2);
    }
    
    .settings-icon.social {
        background-color: rgba(255,255,255,0.2);
    }
    
    .setting-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .setting-item:last-child {
        border-bottom: 0;
    }
    
    .setting-label {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .setting-description {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .setting-value {
        font-size: 0.85rem;
        color: #495057;
        transition: all 0.2s ease;
    }
    
    .btn-settings-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        color: #fff;
    }
    
    .btn-settings-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
        color: #fff;
    }
    
    .btn-settings-secondary {
        background-color: #858796;
        border-color: #858796;
        color: #fff;
    }
    
    .btn-settings-secondary:hover {
        background-color: #717384;
        border-color: #6b6d7d;
        color: #fff;
    }
    
    .settings-tabs .nav-link {
        padding: 0.75rem 1.25rem;
        border-radius: 0.25rem 0.25rem 0 0;
        font-weight: 500;
    }
    
    .settings-tabs .nav-link.active {
        background-color: #f8f9fc;
        border-color: #ddd #ddd #f8f9fc;
        color: #4e73df;
    }
    
    .settings-tab-content {
        background-color: #f8f9fc;
    }
    
    .settings-modal .modal-content {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .settings-modal .modal-header {
        background-color: #4e73df;
        color: #fff;
    }
    
    .settings-modal .modal-title {
        font-weight: 600;
    }
    
    .setting-item.p-3 {
        background-color: #fff;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .setting-item.p-3:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .image-preview-container {
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        text-align: center;
    }
    
    /* Custom color for social media tab */
    .bg-purple {
        background-color: #6f42c1 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="settings-header">
        <h1 class="title">System Settings</h1>
        <p class="description">Configure your application settings and preferences</p>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Settings Overview</h3>
                    <button type="button" class="btn btn-settings-primary" id="openSettingsModal">
                        <i class="fas fa-cog me-1"></i> Edit Settings
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="settings-card">
                                <div class="card-header bg-primary text-white d-flex align-items-center">
                                    <div class="settings-icon general">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <h5 class="mb-0">General Settings</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($generalSettings as $setting)
                                        <div class="setting-item">
                                            <div class="setting-label">{{ $setting->label }}</div>
                                            <div class="setting-description">{{ $setting->description }}</div>
                                            <div class="setting-value">
                                                @if($setting->type == 'checkbox')
                                                    {!! $setting->value ? '<i class="fas fa-check-circle"></i> Enabled' : '<i class="fas fa-times-circle"></i> Disabled' !!}
                                                @elseif($setting->type == 'image')
                                                    @if($setting->value)
                                                        <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}" class="img-thumbnail" style="max-height: 50px;">
                                                    @else
                                                        <span class="text-muted">No image</span>
                                                    @endif
                                                @else
                                                    {{ Str::limit($setting->value, 30) }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="settings-card">
                                <div class="card-header bg-info text-white d-flex align-items-center">
                                    <div class="settings-icon seo">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <h5 class="mb-0">SEO Settings</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($seoSettings as $setting)
                                        <div class="setting-item">
                                            <div class="setting-label">{{ $setting->label }}</div>
                                            <div class="setting-description">{{ $setting->description }}</div>
                                            <div class="setting-value">
                                                @if($setting->type == 'color')
                                                    <span class="badge rounded-pill border d-inline-block" style="background-color: {{ $setting->value }}; width: 30px; height: 30px;"></span>
                                                    <span class="ms-2">{{ $setting->value }}</span>
                                                @elseif($setting->type == 'image')
                                                    @if($setting->value)
                                                        <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}" class="img-thumbnail" style="max-height: 50px;">
                                                    @else
                                                        <span class="text-muted">No image</span>
                                                    @endif
                                                @else
                                                    {{ Str::limit($setting->value, 30) }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="settings-card">
                                <div class="card-header bg-success text-white d-flex align-items-center">
                                    <div class="settings-icon contact">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <h5 class="mb-0">Contact Settings</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($contactSettings as $setting)
                                        <div class="setting-item">
                                            <div class="setting-label">{{ $setting->label }}</div>
                                            <div class="setting-description">{{ $setting->description }}</div>
                                            <div class="setting-value">
                                                {{ Str::limit($setting->value, 30) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="settings-card">
                                <div class="card-header bg-purple text-white d-flex align-items-center">
                                    <div class="settings-icon social">
                                        <i class="fas fa-share-alt"></i>
                                    </div>
                                    <h5 class="mb-0">Social Media</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($socialSettings as $setting)
                                        <div class="setting-item">
                                            <div class="setting-label">
                                                @if(strpos($setting->key, 'facebook') !== false)
                                                    <i class="fab fa-facebook text-primary me-1"></i>
                                                @elseif(strpos($setting->key, 'instagram') !== false)
                                                    <i class="fab fa-instagram text-danger me-1"></i>
                                                @elseif(strpos($setting->key, 'twitter') !== false)
                                                    <i class="fab fa-twitter text-info me-1"></i>
                                                @elseif(strpos($setting->key, 'linkedin') !== false)
                                                    <i class="fab fa-linkedin text-primary me-1"></i>
                                                @elseif(strpos($setting->key, 'youtube') !== false)
                                                    <i class="fab fa-youtube text-danger me-1"></i>
                                                @endif
                                                {{ $setting->label }}
                                            </div>
                                            <div class="setting-value">
                                                @if($setting->type == 'checkbox')
                                                    {!! $setting->value ? '<i class="fas fa-check-circle"></i> Enabled' : '<i class="fas fa-times-circle"></i> Disabled' !!}
                                                @else
                                                    {{ Str::limit($setting->value, 30) }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade settings-modal" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Edit System Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm" class="settings-form">
                        @csrf
                        <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                    <i class="fas fa-cogs me-2"></i>General
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                                    <i class="fas fa-search me-2"></i>SEO
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                                    <i class="fas fa-envelope me-2"></i>Contact
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                                    <i class="fas fa-share-alt me-2"></i>Social Media
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content p-4 border border-top-0 rounded-bottom settings-tab-content" id="settingsTabsContent">
                            <!-- General Settings -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="settings-icon general me-3">
                                        <i class="fas fa-cogs"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">General Settings</h4>
                                        <p class="text-muted mb-0">Configure basic application settings</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    @foreach($generalSettings as $setting)
                                        <div class="col-md-6 mb-4">
                                            <div class="setting-item p-3">
                                        <label for="{{ $setting->key }}" class="form-label">{{ $setting->label }}</label>
                                        
                                                @if($setting->type == 'text' || $setting->type == 'email' || $setting->type == 'number' || $setting->type == 'url')
                                                    <input type="{{ $setting->type }}" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ $setting->value }}">
                                        @elseif($setting->type == 'textarea')
                                            <textarea class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" rows="3">{{ $setting->value }}</textarea>
                                        @elseif($setting->type == 'checkbox')
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" class="form-check-input" id="{{ $setting->key }}" name="{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $setting->key }}"></label>
                                                    </div>
                                                @elseif($setting->type == 'image')
                                                    <div class="mb-3">
                                                        <input type="file" class="form-control file-input" id="{{ $setting->key }}" name="{{ $setting->key }}" accept="image/*">
                                                        @if($setting->value)
                                                            <div class="mt-2 image-preview-container">
                                                                <img id="{{ $setting->key }}_preview" src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}" class="img-thumbnail" style="max-height: 100px;">
                                                                <small class="text-muted d-block">Current: {{ basename($setting->value) }}</small>
                                                            </div>
                                                        @else
                                                            <div class="mt-2 image-preview-container" style="display:none;">
                                                                <img id="{{ $setting->key }}_preview" src="" alt="{{ $setting->label }}" class="img-thumbnail" style="max-height: 100px;">
                                                            </div>
                                                        @endif
                                            </div>
                                        @endif
                                        
                                        @if($setting->description)
                                            <div class="form-text text-muted">{{ $setting->description }}</div>
                                        @endif
                                            </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            
                            <!-- SEO Settings -->
                            <div class="tab-pane fade" id="seo" role="tabpanel">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="settings-icon seo me-3">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">SEO Settings</h4>
                                        <p class="text-muted mb-0">Optimize your site for search engines</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    @foreach($seoSettings as $setting)
                                        <div class="col-md-6 mb-4">
                                            <div class="setting-item p-3">
                                        <label for="{{ $setting->key }}" class="form-label">{{ $setting->label }}</label>
                                        
                                        @if($setting->type == 'text')
                                            <input type="text" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ $setting->value }}">
                                        @elseif($setting->type == 'textarea')
                                            <textarea class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" rows="3">{{ $setting->value }}</textarea>
                                        @elseif($setting->type == 'color')
                                                    <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ $setting->value }}">
                                                        <input type="text" class="form-control" value="{{ $setting->value }}" onchange="document.getElementById('{{ $setting->key }}').value = this.value">
                                                    </div>
                                        @elseif($setting->type == 'image')
                                                    <div class="mb-3">
                                                        <input type="file" class="form-control file-input" id="{{ $setting->key }}" name="{{ $setting->key }}" accept="image/*">
                                                        @if($setting->value)
                                                            <div class="mt-2 image-preview-container">
                                                                <img id="{{ $setting->key }}_preview" src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}" class="img-thumbnail" style="max-height: 60px;">
                                                            </div>
                                                        @else
                                                            <div class="mt-2 image-preview-container" style="display:none;">
                                                                <img id="{{ $setting->key }}_preview" src="" alt="{{ $setting->label }}" class="img-thumbnail" style="max-height: 60px;">
                                                </div>
                                                    @endif
                                                </div>
                                                @endif
                                                
                                                @if($setting->description)
                                                    <div class="form-text text-muted">{{ $setting->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        
                            <!-- Contact Settings -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="settings-icon contact me-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">Contact Settings</h4>
                                        <p class="text-muted mb-0">Manage your contact information and form settings</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    @foreach($contactSettings as $setting)
                                        <div class="col-md-6 mb-4">
                                            <div class="setting-item p-3">
                                                <label for="{{ $setting->key }}" class="form-label">{{ $setting->label }}</label>
                                                
                                                @if($setting->type == 'text')
                                                    <input type="text" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ $setting->value }}">
                                                @elseif($setting->type == 'textarea')
                                                    <textarea class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" rows="3">{{ $setting->value }}</textarea>
                                                @elseif($setting->type == 'email')
                                                    <input type="email" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ $setting->value }}">
                                        @endif
                                        
                                        @if($setting->description)
                                            <div class="form-text text-muted">{{ $setting->description }}</div>
                                        @endif
                                            </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                            
                            <!-- Social Media Settings -->
                            <div class="tab-pane fade" id="social" role="tabpanel">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="settings-icon social me-3">
                                        <i class="fas fa-share-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">Social Media Settings</h4>
                                        <p class="text-muted mb-0">Configure your social media profiles and sharing options</p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    @foreach($socialSettings as $setting)
                                        <div class="col-md-6 mb-4">
                                            <div class="setting-item p-3">
                                                <label for="{{ $setting->key }}" class="form-label">
                                                    @if(strpos($setting->key, 'facebook') !== false)
                                                        <i class="fab fa-facebook text-primary me-2"></i>
                                                    @elseif(strpos($setting->key, 'instagram') !== false)
                                                        <i class="fab fa-instagram text-danger me-2"></i>
                                                    @elseif(strpos($setting->key, 'twitter') !== false)
                                                        <i class="fab fa-twitter text-info me-2"></i>
                                                    @elseif(strpos($setting->key, 'linkedin') !== false)
                                                        <i class="fab fa-linkedin text-primary me-2"></i>
                                                    @elseif(strpos($setting->key, 'youtube') !== false)
                                                        <i class="fab fa-youtube text-danger me-2"></i>
                                                    @elseif(strpos($setting->key, 'pinterest') !== false)
                                                        <i class="fab fa-pinterest text-danger me-2"></i>
                                                    @elseif(strpos($setting->key, 'tiktok') !== false)
                                                        <i class="fab fa-tiktok text-dark me-2"></i>
                                                    @else
                                                        <i class="fas fa-share-alt me-2"></i>
                                                    @endif
                                                    {{ $setting->label }}
                                                </label>
                                                
                                                @if($setting->type == 'url')
                                                    <input type="url" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ $setting->value }}">
                                                @elseif($setting->type == 'checkbox')
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" class="form-check-input" id="{{ $setting->key }}" name="{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="{{ $setting->key }}"></label>
                                                    </div>
                                        @endif
                                        
                                        @if($setting->description)
                                            <div class="form-text text-muted">{{ $setting->description }}</div>
                                        @endif
                                            </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-settings-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
                <button type="button" class="btn btn-settings-primary" id="saveSettingsBtn">
                    <i class="fas fa-save me-1"></i> Save Settings
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        // Initialize the tabs
        $('#settingsTabs button').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        // Open the settings modal
        $('#openSettingsModal').on('click', function() {
            $('#settingsModal').modal('show');
        });
        
        // Save settings form
        $('#saveSettingsBtn').on('click', function() {
            $('#settingsForm').submit();
        });
        
        // Add animation when hovering over setting items
        $('.setting-item').hover(
            function() {
                $(this).find('.setting-value').addClass('fw-bold');
            },
            function() {
                $(this).find('.setting-value').removeClass('fw-bold');
            }
        );

        // Make file inputs more interactive
        $('.file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            
            // Show preview for images
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                var previewId = $(this).attr('id') + '_preview';
                var previewContainer = $(this).closest('.mb-3').find('.image-preview-container');
                
                reader.onload = function(e) {
                    $('#' + previewId).attr('src', e.target.result);
                    previewContainer.show();
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection 