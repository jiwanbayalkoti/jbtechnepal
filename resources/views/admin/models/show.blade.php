@extends('layouts.admin')

@section('title', 'Model Details')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1>Model Details: {{ $model->name }}</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.models.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Models
        </a>
        <a href="{{ route('admin.models.edit', $model->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit Model
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Basic Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th style="width: 30%">ID:</th>
                        <td>{{ $model->id }}</td>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <td>{{ $model->name }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($model->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th style="width: 30%">Brand:</th>
                        <td>{{ $model->brand->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Category:</th>
                        <td>{{ $model->category->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Subcategory:</th>
                        <td>{{ $model->subcategory->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $model->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Description</h5>
            </div>
            <div class="card-body">
                @if($model->description)
                    {{ $model->description }}
                @else
                    <em>No description available</em>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Features</h5>
            </div>
            <div class="card-body">
                @if($model->features && count($model->features) > 0)
                    <ul class="list-group">
                        @foreach($model->features as $feature)
                            <li class="list-group-item">{{ $feature }}</li>
                        @endforeach
                    </ul>
                @else
                    <em>No features listed</em>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Specifications</h5>
    </div>
    <div class="card-body">
        @if($model->specifications && count($model->specifications) > 0)
            <div class="row">
                @foreach($model->specifications as $spec)
                    <div class="col-md-4 mb-2">
                        <div class="card h-100">
                            <div class="card-body">
                                {{ $spec }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <em>No specifications available</em>
        @endif
    </div>
</div>
@endsection 