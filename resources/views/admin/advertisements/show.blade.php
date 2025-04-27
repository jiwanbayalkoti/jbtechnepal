@extends('layouts.admin')

@section('title', 'Advertisement Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Advertisement Details</h1>
        <div>
            <a href="{{ route('admin.advertisements.edit', $advertisement->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit fa-sm text-white-50 mr-1"></i> Edit Advertisement
            </a>
            <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary btn-sm ml-2">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Advertisements
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Advertisement Image -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Advertisement Image</h6>
                </div>
                <div class="card-body text-center">
                    @if($advertisement->image)
                        <img src="{{ asset('storage/' . $advertisement->image) }}" class="img-fluid mb-3" style="max-height: 300px;" alt="{{ $advertisement->title }}">
                        <p class="small text-muted mb-0">Image path: {{ $advertisement->image }}</p>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> No image uploaded for this advertisement.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Advertisement Details -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Advertisement Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 30%">ID</th>
                                <td>{{ $advertisement->id }}</td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td>{{ $advertisement->title }}</td>
                            </tr>
                            <tr>
                                <th>URL</th>
                                <td>
                                    @if($advertisement->url)
                                        <a href="{{ $advertisement->url }}" target="_blank">
                                            {{ $advertisement->url }} <i class="fas fa-external-link-alt ml-1 small"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">No URL specified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Position</th>
                                <td>
                                    @php
                                        $positionLabels = [
                                            'homepage_top' => '<span class="badge badge-primary">Homepage Top</span>',
                                            'homepage_slider' => '<span class="badge badge-info">Homepage Slider</span>',
                                            'homepage_middle' => '<span class="badge badge-secondary">Homepage Middle</span>',
                                            'homepage_bottom' => '<span class="badge badge-dark">Homepage Bottom</span>',
                                            'sidebar' => '<span class="badge badge-success">Sidebar</span>',
                                            'category_page' => '<span class="badge badge-warning">Category Page</span>',
                                            'product_page' => '<span class="badge badge-danger">Product Page</span>'
                                        ];
                                    @endphp
                                    {!! $positionLabels[$advertisement->position] ?? '<span class="badge badge-secondary">' . $advertisement->position . '</span>' !!}
                                </td>
                            </tr>
                            <tr>
                                <th>Display Order</th>
                                <td>{{ $advertisement->display_order }}</td>
                            </tr>
                            <tr>
                                <th>Date Range</th>
                                <td>
                                    @if($advertisement->start_date && $advertisement->end_date)
                                        {{ $advertisement->start_date->format('M d, Y') }} - {{ $advertisement->end_date->format('M d, Y') }}
                                    @elseif($advertisement->start_date)
                                        From {{ $advertisement->start_date->format('M d, Y') }} (No end date)
                                    @elseif($advertisement->end_date)
                                        Until {{ $advertisement->end_date->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">No date range specified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if(!$advertisement->is_active)
                                        <span class="badge badge-danger">Inactive</span>
                                    @elseif($advertisement->end_date && now() > $advertisement->end_date)
                                        <span class="badge badge-warning">Expired</span>
                                    @elseif($advertisement->start_date && now() < $advertisement->start_date)
                                        <span class="badge badge-info">Scheduled</span>
                                    @else
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Statistics</th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <div class="h4 mb-0">{{ number_format($advertisement->views) }}</div>
                                            <div class="small text-muted">Views</div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="h4 mb-0">{{ number_format($advertisement->clicks) }}</div>
                                            <div class="small text-muted">Clicks</div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="h4 mb-0">
                                                {{ $advertisement->views > 0 ? number_format(($advertisement->clicks / $advertisement->views) * 100, 2) : '0.00' }}%
                                            </div>
                                            <div class="small text-muted">CTR</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $advertisement->created_at->format('M d, Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $advertisement->updated_at->format('M d, Y H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($advertisement->content)
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Advertisement Content</h6>
                </div>
                <div class="card-body">
                    <div class="border p-3 rounded">
                        {!! $advertisement->content !!}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <a href="{{ route('admin.advertisements.edit', $advertisement->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i> Edit Advertisement
                        </a>
                        <a href="{{ url('/ad/click/' . $advertisement->id) }}" target="_blank" class="btn btn-info ml-2">
                            <i class="fas fa-external-link-alt mr-1"></i> Test Advertisement Link
                        </a>
                    </div>
                    <form action="{{ route('admin.advertisements.destroy', $advertisement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this advertisement?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Delete Advertisement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Any specific scripts for this page
</script>
@endsection 