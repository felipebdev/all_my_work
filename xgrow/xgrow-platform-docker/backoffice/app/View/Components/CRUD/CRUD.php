<?php

namespace App\View\Components\CRUD;

use Illuminate\View\Component;
use App\View\Components\DOMComponent;

class CRUD extends DOMComponent
{
	/**
	 * CRUD constructor.
	 *
	 * @param string|null $id
	 * @param string|null $name
	 * @param string|null $class
	 * @param array|null $defaultAttributes
	 * @param array|null $dataAttributes
	 */
    public function __construct(?string $id = null, ?string $name = null, ?string $class = null, ?array $defaultAttributes = null, ?array $dataAttributes = null)
    {
	    parent::__construct($id, $name, $class, $defaultAttributes, $dataAttributes);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.crud.crud');
    }
}
