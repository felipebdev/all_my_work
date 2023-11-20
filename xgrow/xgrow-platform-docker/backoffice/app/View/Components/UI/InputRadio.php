<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class InputRadio extends Input
{
	public bool $checked = false;

	/**
	 * InputRadio constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $value
	 * @param string $label
	 * @param string $prepend
	 * @param string $append
	 * @param int $maxlength
	 * @param bool $required
	 * @param string $validation
	 * @param string $tip
	 * @param string $state
	 * @param int $schema
	 * @param string|null $checked
	 */
	public function __construct(string $id = '', string $name = '', string $class = '', string $value = '', string $label = '', string $prepend = '', string $append = '', $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, bool $checked = false, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false)
	{
		parent::__construct($id, $name, $class, 'radio', $value, $label, $prepend, $append, $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $prependIcon, $group, $optional, $readonly);

		$this->class .= 'form-check-input';
		$this->checked = $checked;
	}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.input-radio');
    }
}
