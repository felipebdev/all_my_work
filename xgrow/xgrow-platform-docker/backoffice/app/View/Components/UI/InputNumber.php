<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class InputNumber extends Input
{
	/**
	 * InputNumber constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $value
	 * @param string $label
	 * @param string $append
	 * @param $maxlength
	 * @param bool $required
	 * @param string $validation
	 * @param string $tip
	 * @param string $state
	 * @param $schema
	 * @param string|null $prependIcon
	 * @param string $group
	 * @param string|null $optional
	 * @param bool $readonly
	 * @param string $numberType
	 */
	public function __construct(string $id = '', string $name = '', string $class = '', string $type = 'number', string $value = '', string $label = '', string $append = '', $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false)
	{
		parent::__construct($id, $name, $class, $type, $value, $label, '<i class="fas fa-sort-numeric-up"></i>', $append, $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $prependIcon, $group, $optional, $readonly);
	}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.input-number');
    }
}
