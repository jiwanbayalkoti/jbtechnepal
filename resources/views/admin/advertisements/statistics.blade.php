@extends('layouts.admin')

@section('title', 'Advertisement Statistics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Advertisement Statistics</h1>
        <div>
            <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Advertisements
            </a>
            <a href="{{ route('admin.advertisements.export') }}" class="btn btn-success btn-sm ml-2">
                <i class="fas fa-file-csv fa-sm text-white-50 mr-1"></i> Export Data
            </a>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Impressions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalViews) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Clicks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalClicks) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Average CTR</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgCtr }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Ads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeAdsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ad fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="row">
        <!-- CTR by Position -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">CTR by Position</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="positionCtrChart" style="min-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing Ads -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performing Ads (by CTR)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="topPerformingChart" style="min-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Advertisement Performance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Clicks</th>
                            <th>CTR</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($advertisements as $ad)
                        <tr>
                            <td>{{ $ad->id }}</td>
                            <td>
                                <a href="{{ route('admin.advertisements.show', $ad->id) }}">{{ $ad->title }}</a>
                            </td>
                            <td>{{ $ad->position_name }}</td>
                            <td>{!! $ad->status_badge !!}</td>
                            <td>{{ number_format($ad->views) }}</td>
                            <td>{{ number_format($ad->clicks) }}</td>
                            <td>
                                @if($ad->views > 0)
                                    {{ number_format(($ad->clicks / $ad->views) * 100, 2) }}%
                                @else
                                    0.00%
                                @endif
                            </td>
                            <td>
                                @if($ad->start_date && $ad->end_date)
                                    {{ $ad->start_date->format('M d, Y') }} - {{ $ad->end_date->format('M d, Y') }}
                                @elseif($ad->start_date)
                                    From {{ $ad->start_date->format('M d, Y') }}
                                @elseif($ad->end_date)
                                    Until {{ $ad->end_date->format('M d, Y') }}
                                @else
                                    No date restrictions
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Position CTR Chart
        const positionCtrCtx = document.getElementById('positionCtrChart').getContext('2d');
        new Chart(positionCtrCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($positionStats->pluck('position_name')) !!},
                datasets: [{
                    label: 'Click-Through Rate (%)',
                    data: {!! json_encode($positionStats->pluck('ctr')) !!},
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(255, 159, 64, 0.5)',
                        'rgba(255, 99, 132, 0.5)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });

        // Top Performing Ads Chart
        const topPerformingCtx = document.getElementById('topPerformingChart').getContext('2d');
        new Chart(topPerformingCtx, {
            type: 'horizontalBar',
            data: {
                labels: {!! json_encode($topAds->pluck('title')) !!},
                datasets: [{
                    label: 'Click-Through Rate (%)',
                    data: {!! json_encode($topAds->pluck('ctr')) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.x.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });

        // Initialize DataTables
        $('#dataTable').DataTable({
            order: [[6, 'desc']] // Sort by CTR by default
        });
    });
</script>
@endsection 