<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ButtonLink extends Component
{
    public $size;
    public $buttonType;
    public $className;
    public $icon;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($buttonType = 'default', $size = null, $icon = null, $className = null)
    {
        $this->size = $size;
        $this->size = $buttonType;
        $this->icon = $icon;
        $this->className = $className;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.inputs.button-link');
    }
}
