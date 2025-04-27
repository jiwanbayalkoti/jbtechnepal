@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Contact Messages</h2>
        @if($pendingCount > 0)
            <span class="badge bg-danger">{{ $pendingCount }} pending messages</span>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($messages->isEmpty())
            <div class="alert alert-info mb-0">
                No contact messages found.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            <tr class="{{ $message->status === 'pending' ? 'table-warning' : '' }}">
                                <td>{{ $message->id }}</td>
                                <td>{{ $message->name }}</td>
                                <td>{{ $message->email }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($message->subject, 30) }}</td>
                                <td>{{ $message->created_at->format('M d, Y') }}</td>
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
                                    <a href="{{ route('admin.contact.show', $message->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.contact.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 