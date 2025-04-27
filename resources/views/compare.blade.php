@extends('layouts.app')

@section('title', 'Product Comparison')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Product Comparison</h1>
    <div>
        <a href="{{ route('clear.compare') }}" class="btn btn-outline-danger me-2">
            <i class="fas fa-trash me-1"></i>Clear All
        </a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Home
        </a>
    </div>
</div>

@if($products->count() < 2)
    <div class="alert alert-warning">
        <h4 class="alert-heading">Not enough products!</h4>
        <p>You need at least 2 products to make a comparison. Please add more products to your comparison list.</p>
        <hr>
        <p class="mb-0">
            <a href="{{ route('categories.show', $products->first()->category->slug) }}" class="btn btn-primary">
                Add more {{ $products->first()->category->name }}
            </a>
        </p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered comparison-table">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 200px;"></th>
                    @foreach($products as $product)
                        <th class="text-center" style="min-width: 250px;">
                            <div class="position-relative">
                                <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-1 remove-from-compare" data-product-id="{{ $product->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                                
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid mx-auto mb-2" style="max-height: 120px;" alt="{{ $product->name }}">
                                @else
                                    <div class="py-3">
                                        <i class="fas fa-laptop fa-4x text-secondary"></i>
                                    </div>
                                @endif
                                
                                <h5>{{ $product->name }}</h5>
                                <h6 class="text-primary">${{ number_format($product->price, 2) }}</h6>
                                
                                <a href="{{ route('product', $product->slug) }}" class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Brand</th>
                    @foreach($products as $product)
                        <td class="text-center">{{ $product->brand ?? 'N/A' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <th>Model</th>
                    @foreach($products as $product)
                        <td class="text-center">{{ $product->model ?? 'N/A' }}</td>
                    @endforeach
                </tr>
                
                @foreach($specMatrix as $typeId => $spec)
                    <tr>
                        <th>{{ $spec['name'] }}</th>
                        @foreach($products as $product)
                            <td class="text-center">
                                {{ $spec['values'][$product->id] }}
                                @if($spec['unit'] && $spec['values'][$product->id] != 'N/A')
                                    {{ $spec['unit'] }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<!-- AI Product Recommendations -->
<div class="card mt-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-robot me-2"></i>AI Product Recommendations
        </h5>
    </div>
    <div class="card-body">
        <div id="aiRecommendationsResult">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <span id="recommendationSummary"></span>
            </div>
            
            <div id="productRecommendations"></div>
            
            <div class="alert alert-success mt-3">
                <i class="fas fa-check-circle me-2"></i>
                <span id="overallRecommendation"></span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Function to get AI recommendations
    function getAiRecommendations() {
        const productIds = [];
        $('.comparison-table th').each(function() {
            const productId = $(this).find('button.remove-from-compare').data('product-id');
            if (productId) {
                productIds.push(productId);
            }
        });
        
        console.log('Getting recommendations for products:', productIds);
        
        if (productIds.length === 0) {
            $('#aiRecommendationsResult').hide();
            return;
        }
        
        // Show loading state
        $('#aiRecommendationsResult').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Analyzing products and generating recommendations...</p>
            </div>
        `).show();
        
        // Make AJAX request
        $.ajax({
            url: '{{ route("ai.recommendations") }}',
            method: 'POST',
            data: {
                product_ids: productIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('AI Recommendations response:', response);
                if (response.success) {
                    // Update summary
                    $('#recommendationSummary').text(response.summary);
                    
                    // Clear and update product recommendations
                    const $recommendations = $('#productRecommendations');
                    $recommendations.empty();
                    
                    if (response.recommendations && response.recommendations.length > 0) {
                        response.recommendations.forEach(function(rec) {
                            const $card = $('<div class="card mb-3">')
                                .append($('<div class="card-header">').text(rec.product_name))
                                .append($('<div class="card-body">')
                                    .append($('<p class="mb-3">').text(rec.explanation))
                                    .append($('<div class="row">')
                                        .append($('<div class="col-md-6">')
                                            .append($('<h6 class="text-success">').text('Pros:'))
                                            .append($('<ul class="list-unstyled">').append(
                                                rec.pros.map(pro => $('<li>').html('<i class="fas fa-check text-success me-2"></i>' + pro))
                                            ))
                                        )
                                        .append($('<div class="col-md-6">')
                                            .append($('<h6 class="text-danger">').text('Cons:'))
                                            .append($('<ul class="list-unstyled">').append(
                                                rec.cons.map(con => $('<li>').html('<i class="fas fa-times text-danger me-2"></i>' + con))
                                            ))
                                        )
                                    )
                                );
                            
                            $recommendations.append($card);
                        });
                    } else {
                        $recommendations.html('<div class="alert alert-warning">No recommendations available for these products.</div>');
                    }
                    
                    // Update overall recommendation
                    $('#overallRecommendation').text(response.overall_recommendation);
                } else {
                    $('#aiRecommendationsResult').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>${response.message || 'Failed to generate recommendations. Please try again.'}
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AI Recommendations error:', error);
                console.error('Response:', xhr.responseText);
                $('#aiRecommendationsResult').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>An error occurred while generating recommendations. Please try again.
                        <br>
                        <small class="text-muted">${error}</small>
                    </div>
                `);
            }
        });
    }

    // Call getAiRecommendations when page loads
    getAiRecommendations();

    // Add event listeners for product removal
    $(document).on('click', '.remove-from-compare', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        const $th = $(this).closest('th');
        
        $.ajax({
            url: '{{ route("remove.from.compare") }}',
            method: 'POST',
            data: {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $th.remove();
                    if ($('.comparison-table th').length <= 1) {
                        window.location.href = '{{ route("home") }}';
                    } else {
                        getAiRecommendations();
                    }
                }
            }
        });
    });
});
</script>
@endsection 