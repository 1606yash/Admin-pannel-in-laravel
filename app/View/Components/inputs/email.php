<?php

namespace App\View\Components\inputs;

use Illuminate\View\Component;

class email extends Component
{
    public $for;
    public $label;
    public $value;
    public $id;
    public $name;
    public $icon;
    public $formNote;
    public $required;
    public $class;
    

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $for = '',
        $label = '',
        $value = '',
        $id = '',
        $name = '',
        $icon = '',
        $formNote = '',
        $required = 'false',
        $class = ''
    ) {
        $this->for = $for;
        $this->label = $label;
        $this->value = $value;
        $this->id = $id;
        $this->name = $name;
        $this->icon = $icon;
        $this->formNote = $formNote;
        $this->required = $required;
        $this->class = $class;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.inputs.email');
    }
}
