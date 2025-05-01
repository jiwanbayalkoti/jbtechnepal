@extends('layouts.admin')

@section('title', 'Process Return Request')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Process Return #{{ $return->return_number }}</h1>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Returns
        </a>
    </div>

    <form action="{{ route('admin.returns.update', $return) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Return Information -->
            <div class="col-xl-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Return Information</h6>
                    </div>
                    <div class="card-body">
                        <!-- Status -->
                        <div class="form-group mb-3">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach(['requested','approved','received','processed','completed','rejected'] as $status)
                                    <option value="{{ $status }}" {{ $return->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Refund Details (show for processed or completed) -->
                        @if(in_array($return->status, ['processed','completed']))
                        <div class="form-group mb-3">
                            <label for="refund_method">Refund Method <span class="text-danger">*</span></label>
                            <select name="refund_method" id="refund_method" class="form-select @error('refund_method') is-invalid @enderror" required>
                                @foreach(['credit','original_payment','exchange'] as $method)
                                    <option value="{{ $method }}" {{ ($return->refund_method ?? '') === $method ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $method)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('refund_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="refund_amount">Refund Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="refund_amount" id="refund_amount" 
                                value="{{ old('refund_amount', $return->refund_amount) }}" 
                                class="form-control @error('refund_amount') is-invalid @enderror" required>
                            @error('refund_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @endif

                        <!-- Tracking Number -->
                        <div class="form-group mb-3">
                            <label for="return_tracking_number">Tracking Number</label>
                            <input type="text" name="return_tracking_number" id="return_tracking_number"
                                value="{{ old('return_tracking_number', $return->return_tracking_number) }}"
                                class="form-control @error('return_tracking_number') is-invalid @enderror">
                            @error('return_tracking_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Admin Notes -->
                        <div class="form-group mb-3">
                            <label for="admin_notes">Admin Notes</label>
                            <textarea name="admin_notes" id="admin_notes" rows="3"
                                class="form-control @error('admin_notes') is-invalid @enderror">{{ old('admin_notes', $return->admin_notes) }}</textarea>
                            @error('admin_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Update Return
                        </button>
                    </div>
                </div>
            </div>

            <!-- Return Items -->
            <div class="col-xl-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Return Items</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Condition</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($return->items as $item)
                                    <tr>
                                        <td>{{ $item->orderItem->product_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ ucfirst($item->condition) }}</td>
                                        <td>{{ $item->reason }}</td>
                                        <td>
                                            @if($item->approved)
                                                <span class="badge bg-success text-white">Approved</span>
                                            @else
                                                <span class="badge bg-warning text-white">Pending</span>
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
        </div>
    </form>
</div>
@endsection 