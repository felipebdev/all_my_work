<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class SelectOption extends HTMLComponent
{
	public ?string $selected = null;
	/**
	 * @var string
	 */
	public string $value = '';
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?string $id = '', ?string $name = '', string $class = '', string $value = '', ?string $selected = null)
    {
        parent::__construct('options', false, $id, $name, $class);
		$this->value = $value;
		$this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.select-option');
    }
}
