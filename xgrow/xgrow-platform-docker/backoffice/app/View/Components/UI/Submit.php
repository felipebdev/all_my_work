<?php

namespace App\View\Components\UI;

use App\View\Components\HTML\Button;

class Submit extends Button
{
	/**
	 * Submit constructor.
	 *
	 * @param string|null $id
	 * @param string|null $name
	 * @param string $class
	 */
    public function __construct(?string $id = '', ?string $name = '', string $class = 'btn btn-primary clearfix')
    {
        parent::__construct($id, $name, $class, 'submit');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.submit');
    }
}
