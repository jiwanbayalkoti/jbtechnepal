<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminFormModal extends Component
{
    /**
     * The modal ID.
     *
     * @var string
     */
    public $id;
    
    /**
     * The modal title.
     *
     * @var string
     */
    public $title;
    
    /**
     * The modal size.
     *
     * @var string
     */
    public $size;
    
    /**
     * The form ID.
     *
     * @var string
     */
    public $formId;
    
    /**
     * The form action.
     *
     * @var string
     */
    public $formAction;
    
    /**
     * The form method.
     *
     * @var string
     */
    public $formMethod;
    
    /**
     * Whether the form has files.
     *
     * @var bool
     */
    public $hasFiles;
    
    /**
     * The submit button text.
     *
     * @var string
     */
    public $submitButtonText;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $id = 'formModal',
        $title = 'Form',
        $size = 'lg',
        $formId = 'modalForm',
        $formAction = '#',
        $formMethod = 'POST',
        $hasFiles = false,
        $submitButtonText = 'Save'
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->size = $size;
        $this->formId = $formId;
        $this->formAction = $formAction;
        $this->formMethod = $formMethod;
        $this->hasFiles = $hasFiles;
        $this->submitButtonText = $submitButtonText;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin-form-modal');
    }
} 