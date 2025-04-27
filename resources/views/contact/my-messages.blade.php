@extends('layouts.app')

@section('title', 'My Messages')

@section('content')
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">My Messages</h4>
            <a href="{{ route('contact.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-paper-plane me-1"></i>Send New Message
            </a>
        </div>
        <div class="card-body">
            @if($messages->isEmpty())
                <div class="alert alert-info">
                    <p class="mb-0">You haven't sent any messages yet. <a href="{{ route('contact.index') }}">Send your first message</a>.</p>
                </div>
            @else
                <div class="table-responsive mb-4">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr>
                                    <td>{{ $message->created_at->format('M d, Y') }}</td>
                                    <td>{{ $message->subject }}</td>
                                    <td>
                                        @if($message->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($message->status === 'read')
                                            <span class="badge bg-primary">Read</span>
                                        @else
                                            <span class="badge bg-success">Replied</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary view-message" data-bs-toggle="modal" data-bs-target="#messageModal{{ $message->id }}">
                                            <i class="fas fa-eye me-1"></i>View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $messages->links() }}

                <!-- Message Modals -->
                @foreach($messages as $message)
                    <div class="modal fade" id="messageModal{{ $message->id }}" tabindex="-1" aria-labelledby="messageModalLabel{{ $message->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="messageModalLabel{{ $message->id }}">{{ $message->subject }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0 text-primary">Your Message</h6>
                                            <small class="text-muted">{{ $message->created_at->format('F d, Y h:i A') }}</small>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <p style="white-space: pre-line;">{{ $message->message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($message->admin_reply)
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 text-success">Admin Reply</h6>
                                                <small class="text-muted">{{ $message->updated_at->format('F d, Y h:i A') }}</small>
                                            </div>
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <p style="white-space: pre-line;">{{ $message->admin_reply }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>No reply yet. We'll notify you when we respond.
                                        </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <a href="{{ route('contact.index') }}" class="btn btn-primary">
                                        <i class="fas fa-reply me-1"></i>Send New Message
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection 