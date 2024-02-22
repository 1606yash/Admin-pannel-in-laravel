<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $buttonType;
    public $size;
    public $className;
    public $icon;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($buttonType = 'default', $size = null, $icon = null, $className = null)
    {
        $this->buttonType = $buttonType;
        $this->size = $size;
        $this->className = $className;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.inputs.button');
    }
}
