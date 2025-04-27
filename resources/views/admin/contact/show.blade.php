@extends('layouts.admin')

@section('title', 'View Message')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Message Details</h2>
    <a href="{{ route('admin.contact.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Messages
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sender Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold">Name:</label>
                    <p>{{ $message->name }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="fw-bold">Email:</label>
                    <p>
                        <a href="mailto:{{ $message->email }}">{{ $message->email }}</a>
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="fw-bold">Date Sent:</label>
                    <p>{{ $message->created_at->format('F d, Y h:i A') }}</p>
                </div>
                
                <div>
                    <label class="fw-bold">Status:</label>
                    <p>
                        @if($message->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($message->status === 'read')
                            <span class="badge bg-primary">Read</span>
                        @else
                            <span class="badge bg-success">Replied</span>
                        @endif
                    </p>
                </div>
                
                @if($message->user_id)
                    <div class="mt-3 pt-3 border-top">
                        <label class="fw-bold">Registered User:</label>
                        <p>Yes (ID: {{ $message->user_id }})</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ $message->subject }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-4 pb-4 border-bottom">
                    <h6 class="text-muted mb-3">Original Message:</h6>
                    <p style="white-space: pre-line;">{{ $message->message }}</p>
                </div>
                
                @if($message->admin_reply)
                    <div class="mb-4">
                        <h6 class="text-muted mb-3">Your Reply:</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p style="white-space: pre-line;">{{ $message->admin_reply }}</p>
                                <small class="text-muted">
                                    Replied on {{ $message->updated_at->format('F d, Y h:i A') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if($message->status !== 'replied')
                    <form action="{{ route('admin.contact.reply', $message->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="admin_reply" class="form-label">Reply to this message:</label>
                            <textarea class="form-control @error('admin_reply') is-invalid @enderror" id="admin_reply" name="admin_reply" rows="5" required>{{ old('admin_reply') }}</textarea>
                            @error('admin_reply')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-reply me-1"></i>Send Reply
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.contact.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Messages
            </a>
            
            <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Delete Message
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 