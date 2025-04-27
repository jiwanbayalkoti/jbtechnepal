@extends('layouts.admin')

@section('title', 'API Imports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">API Product Imports</h5>
                </div>
                <div class="card-body">
                    <p>Import products directly from brand APIs. Configure API settings in your environment file.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($availableApis as $key => $api)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header {{ $api['enabled'] ? 'bg-success' : 'bg-secondary' }} text-white">
                        <h5 class="card-title mb-0">{{ $api['name'] }}</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $api['description'] }}</p>
                        <div class="mb-3">
                            <strong>Status:</strong> 
                            @if($api['enabled'])
                                <span class="badge bg-success">Enabled</span>
                            @else
                                <span class="badge bg-secondary">Disabled</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong>Endpoint:</strong> 
                            <span class="text-muted">{{ $api['endpoint'] }}</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary import-api-btn" 
                                data-api="{{ $key }}" 
                                {{ !$api['enabled'] ? 'disabled' : '' }}>
                            <i class="fas fa-download me-1"></i> Import Products
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Imports</h5>
                </div>
                <div class="card-body">
                    @if(count($recentImports) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>API</th>
                                        <th>Status</th>
                                        <th>Products</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentImports as $import)
                                        <tr>
                                            <td>{{ $import->created_at }}</td>
                                            <td>{{ $import->api }}</td>
                                            <td>
                                                @if($import->status == 'success')
                                                    <span class="badge bg-success">Success</span>
                                                @elseif($import->status == 'partial')
                                                    <span class="badge bg-warning">Partial</span>
                                                @else
                                                    <span class="badge bg-danger">Error</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($import->status != 'error')
                                                    {{ $import->stats->created ?? 0 }} created,
                                                    {{ $import->stats->updated ?? 0 }} updated
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info view-import-details"
                                                        data-import-id="{{ $import->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No import history available. Start importing products using the options above.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to import products from <span id="apiName"></span>?</p>
                <p>This process may take some time depending on the number of products.</p>
                
                <div id="importOptions" class="mt-3">
                    <h6>Import Options</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="updateExisting" name="options[update_existing]" value="1" checked>
                        <label class="form-check-label" for="updateExisting">
                            Update existing products
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="downloadImages" name="options[download_images]" value="1" checked>
                        <label class="form-check-label" for="downloadImages">
                            Download product images
                        </label>
                    </div>
                </div>
                
                <div id="importProgress" class="mt-3 d-none">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                    </div>
                    <p class="text-center mt-2" id="progressText">Connecting to API...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="startImport">Start Import</button>
            </div>
        </div>
    </div>
</div>

<!-- Import Result Modal -->
<div class="modal fade" id="importResultModal" tabindex="-1" aria-labelledby="importResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importResultModalLabel">Import Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="importSuccess" class="alert alert-success d-none">
                    <h5><i class="fas fa-check-circle me-2"></i> Import Completed</h5>
                    <p id="importSuccessMessage"></p>
                </div>
                
                <div id="importError" class="alert alert-danger d-none">
                    <h5><i class="fas fa-exclamation-circle me-2"></i> Import Failed</h5>
                    <p id="importErrorMessage"></p>
                </div>
                
                <div class="card mt-3 d-none" id="importStats">
                    <div class="card-header">
                        <h6 class="mb-0">Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center mb-3">
                                <h5 id="statTotal">0</h5>
                                <p class="text-muted mb-0">Total Products</p>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <h5 id="statCreated">0</h5>
                                <p class="text-muted mb-0">Created</p>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <h5 id="statUpdated">0</h5>
                                <p class="text-muted mb-0">Updated</p>
                            </div>
                            <div class="col-md-3 text-center mb-3">
                                <h5 id="statFailed">0</h5>
                                <p class="text-muted mb-0">Failed</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 d-none" id="errorDetails">
                    <h6>Error Details</h6>
                    <div class="alert alert-secondary">
                        <ul id="errorList" class="mb-0"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-primary">View Products</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Import button click handler
        $('.import-api-btn').on('click', function() {
            const apiKey = $(this).data('api');
            // Get API name from card title
            const apiName = $(this).closest('.card').find('.card-title').text();
            
            // Update modal
            $('#apiName').text(apiName);
            $('#importModal').modal('show');
            
            // Store API key for later use
            $('#startImport').data('api', apiKey);
        });
        
        // Start import click handler
        $('#startImport').on('click', function() {
            const apiKey = $(this).data('api');
            
            // Get options
            const options = {
                update_existing: $('#updateExisting').is(':checked'),
                download_images: $('#downloadImages').is(':checked')
            };
            
            // Show progress
            $('#importOptions').addClass('d-none');
            $('#importProgress').removeClass('d-none');
            $(this).prop('disabled', true);
            
            // Make AJAX request
            $.ajax({
                url: '{{ route('admin.imports.run') }}',
                type: 'POST',
                data: {
                    api: apiKey,
                    options: options,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Hide import modal
                    $('#importModal').modal('hide');
                    
                    // Show success in result modal
                    $('#importSuccess').removeClass('d-none');
                    $('#importError').addClass('d-none');
                    $('#importSuccessMessage').text(response.message);
                    
                    // Show stats
                    $('#importStats').removeClass('d-none');
                    $('#statTotal').text(response.stats.total);
                    $('#statCreated').text(response.stats.created);
                    $('#statUpdated').text(response.stats.updated);
                    $('#statFailed').text(response.stats.failed);
                    
                    // Show errors if any
                    if (response.stats.errors && response.stats.errors.length > 0) {
                        $('#errorDetails').removeClass('d-none');
                        const $errorList = $('#errorList').empty();
                        
                        response.stats.errors.forEach(function(error) {
                            $errorList.append($('<li>').text(error));
                        });
                    } else {
                        $('#errorDetails').addClass('d-none');
                    }
                    
                    // Show result modal
                    $('#importResultModal').modal('show');
                },
                error: function(xhr) {
                    // Hide import modal
                    $('#importModal').modal('hide');
                    
                    // Show error in result modal
                    $('#importSuccess').addClass('d-none');
                    $('#importError').removeClass('d-none');
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        $('#importErrorMessage').text(response.message || 'An unknown error occurred.');
                    } catch (e) {
                        $('#importErrorMessage').text('An unknown error occurred during import.');
                    }
                    
                    // Hide stats and show result modal
                    $('#importStats').addClass('d-none');
                    $('#errorDetails').addClass('d-none');
                    $('#importResultModal').modal('show');
                },
                complete: function() {
                    // Reset import modal for future use
                    $('#importProgress').addClass('d-none');
                    $('#importOptions').removeClass('d-none');
                    $('#startImport').prop('disabled', false);
                }
            });
        });
    });
</script>
@endsection 