<?php

namespace App\View\Components\HTML;

use App\View\Components\HTMLComponent;
use function view;

class Button extends HTMLComponent
{
	public string $type;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?string $id = '', ?string $name = '', string $class = 'btn btn-primary', string $type = 'button')
    {
        parent::__construct('button', false, $id, $name, $class);
		$this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.html.button');
    }
}
