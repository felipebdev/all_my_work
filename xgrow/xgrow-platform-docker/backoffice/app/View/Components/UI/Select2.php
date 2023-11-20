<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class Select2 extends Select
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
	public function __construct(string $id = '', string $name = '', string $class = '', string $value = '', string $label = '', ?string $prepend = null, ?string $append = null, $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false, string $valueIdentifier = '', string $labelIdentifier = '', bool $multiple = false, array $options = [])
	{
		parent::__construct($id, $name, $class, 'select2', $value, $label, $prepend, $append, $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $prependIcon, $group, $optional, $readonly, $valueIdentifier, $labelIdentifier, $multiple, $options);

		$this->defaultAttributes['data-type'] = 'select2';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.select2');
    }
}
