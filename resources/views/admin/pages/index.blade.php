@extends('layouts.admin')

@section('title', 'Page Management')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Page Management</h1>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle mr-1"></i> Add New Page
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Pages</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pagesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                        <tr>
                            <td>{{ $page->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($page->featured_image)
                                    <img src="{{ asset('storage/' . $page->featured_image) }}" 
                                         alt="{{ $page->title }}" 
                                         class="mr-2"
                                         style="width: 50px; height: 30px; object-fit: cover;">
                                    @else
                                    <div class="bg-gray-200 mr-2" 
                                         style="width: 50px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-file-alt text-gray-500"></i>
                                    </div>
                                    @endif
                                    {{ $page->title }}
                                    @if(in_array($page->slug, ['about-us', 'contact-us']))
                                    <span class="badge badge-info ml-2">System</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <code>{{ $page->slug }}</code>
                                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-primary ml-2">
                                    <i class="fas fa-external-link-alt" title="View Page"></i>
                                </a>
                            </td>
                            <td>{!! $page->status_badge !!}</td>
                            <td>{{ $page->updated_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!in_array($page->slug, ['about-us', 'contact-us']))
                                    <button type="button" class="btn btn-sm btn-danger delete-page" 
                                            data-page-id="{{ $page->id }}" 
                                            data-page-title="{{ $page->title }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $pages->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Page Modal -->
<div class="modal fade" id="deletePageModal" tabindex="-1" role="dialog" aria-labelledby="deletePageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePageModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the page <span id="delete-page-title" class="font-weight-bold"></span>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="delete-page-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Page</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#pagesTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25
        });
        
        // Setup delete page modal
        $('.delete-page').click(function() {
            const pageId = $(this).data('page-id');
            const pageTitle = $(this).data('page-title');
            
            $('#delete-page-title').text(pageTitle);
            $('#delete-page-form').attr('action', `{{ url('admin/pages') }}/${pageId}`);
            $('#deletePageModal').modal('show');
        });
    });
</script>
@endsection 