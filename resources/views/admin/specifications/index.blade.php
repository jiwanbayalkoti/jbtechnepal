@extends('layouts.admin')

@section('title', 'Specification Types - ' . $category->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $category->name }} - Specification Types</h2>
    <div>
        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-outline-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i>Back to Category
        </a>
        <button type="button" class="btn btn-primary" data-open-modal="createSpecificationModal">
            <i class="fas fa-plus me-1"></i>Add Specification Type
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Specification Types</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Unit</th>
                        <th>Comparable</th>
                        <th>Display Order</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($specificationTypes as $specType)
                        <tr>
                            <td>{{ $specType->id }}</td>
                            <td>{{ $specType->name }}</td>
                            <td>{{ $specType->unit ?? '-' }}</td>
                            <td>
                                @if($specType->is_comparable)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>{{ $specType->display_order }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary" 
                                            data-edit-url="{{ route('admin.specifications.edit', [$category->id, $specType->id]) }}"
                                            data-open-modal="editSpecificationModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.specifications.destroy', [$category->id, $specType->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-delete-confirm="Are you sure you want to delete this specification type?">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No specification types found for this category.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">What are Specification Types?</h5>
    </div>
    <div class="card-body">
        <p>Specification types define the characteristics that can be compared between products in this category.</p>
        <ul>
            <li><strong>Name:</strong> The name of the specification (e.g., "Processor", "RAM", "Screen Size")</li>
            <li><strong>Unit:</strong> The measurement unit (e.g., "GB", "inches", "GHz")</li>
            <li><strong>Comparable:</strong> Whether this specification should appear in the comparison table</li>
            <li><strong>Display Order:</strong> The order in which this specification appears in the comparison table</li>
        </ul>
    </div>
</div>

<!-- Create Specification Modal -->
<x-admin-form-modal 
    id="createSpecificationModal" 
    title="Add Specification Type" 
    formId="createSpecificationForm" 
    formAction="{{ route('admin.specifications.store', $category->id) }}" 
    formMethod="POST"
    submitButtonText="Save Specification Type">

    <div class="mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Processor, RAM, Screen Size">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="unit" class="form-label">Unit</label>
        <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit') }}" placeholder="e.g. GB, inches, GHz">
        <small class="form-text text-muted">Leave empty if no unit is needed</small>
        @error('unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_comparable" name="is_comparable" value="1" {{ old('is_comparable') ? 'checked' : '' }} checked>
            <label class="form-check-label" for="is_comparable">
                Comparable (show in comparison table)
            </label>
        </div>
    </div>
    
    <div class="mb-3">
        <label for="display_order" class="form-label">Display Order</label>
        <input type="number" class="form-control @error('display_order') is-invalid @enderror" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" min="0">
        <small class="form-text text-muted">Lower numbers will appear first in the comparison table</small>
        @error('display_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</x-admin-form-modal>

<!-- Edit Specification Modal -->
<x-admin-form-modal 
    id="editSpecificationModal" 
    title="Edit Specification Type" 
    formId="editSpecificationForm" 
    formMethod="POST"
    submitButtonText="Update Specification Type">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading specification type data...</p>
    </div>
</x-admin-form-modal>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // When Edit button is clicked, update the form action
        const editButtons = document.querySelectorAll('[data-edit-url]');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-edit-url');
                if (url) {
                    // This will be processed by the admin-modals.js script
                    // Form will be loaded via AJAX
                    const formAction = url.replace('/edit', '');
                    const editForm = document.getElementById('editSpecificationForm');
                    editForm.action = formAction;
                    
                    // Add method override for PUT
                    let methodInput = editForm.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        editForm.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';
                }
            });
        });
    });
</script>
@endsection 