{{-- resources/views/components/admin-form-modal.blade.php --}}
@props([
    'id' => 'formModal',
    'title' => 'Form',
    'size' => 'lg',
    'formId' => 'modalForm',
    'formAction' => '#',
    'formMethod' => 'POST',
    'hasFiles' => false,
    'submitButtonText' => 'Save'
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }} modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="{{ $formId }}" action="{{ $formAction }}" method="{{ $formMethod }}" 
                      @if($hasFiles) enctype="multipart/form-data" @endif>
                    @if(strtoupper($formMethod) !== 'GET')
                        @csrf
                    @endif
                    @if(strtoupper($formMethod) === 'PUT' || strtoupper($formMethod) === 'PATCH')
                        @method($formMethod)
                    @endif

                    {{ $slot }}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary submit-modal-form" data-form-id="{{ $formId }}">
                    {{ $submitButtonText }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure the form is created when the modal is opened
    const modal = document.getElementById('{{ $id }}');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function() {
            // Verify form exists and enable submit button if it does
            const form = document.getElementById('{{ $formId }}');
            const submitBtn = modal.querySelector('.submit-modal-form');
            
            if (form && submitBtn) {
                submitBtn.disabled = false;
            } else if (submitBtn) {
                // If form doesn't exist yet, disable the submit button temporarily
                submitBtn.disabled = true;
                
                // Check again after a short delay (for dynamically loaded content)
                setTimeout(() => {
                    const delayedCheck = document.getElementById('{{ $formId }}');
                    if (delayedCheck) {
                        submitBtn.disabled = false;
                    }
                }, 500);
            }
        });
    }
});
</script> 