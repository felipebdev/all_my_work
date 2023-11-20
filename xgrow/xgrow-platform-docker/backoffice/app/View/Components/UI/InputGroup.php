<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class InputGroup extends HTMLComponent
{
	/**
	 * @var bool
	 */
	public bool $horizontal;
	/**
	 * @var string
	 */
	public string $label;
	/**
	 * InputGroup constructor.
	 *
	 * @param string|null $id
	 * @param string|null $name
	 * @param string $class
	 * @param string $label
	 * @param bool $horizontal
	 */
    public function __construct(?string $id = '', ?string $name = '', string $class = '', string $label = '', bool $horizontal = false)
    {
        parent::__construct('div', false, $id, $name, $class);
		$this->label = $label;
		$this->horizontal = $horizontal;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.input-group');
    }
}
