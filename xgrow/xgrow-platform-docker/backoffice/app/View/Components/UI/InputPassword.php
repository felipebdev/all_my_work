<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class InputPassword extends Input
{
	/**
	 * InputPassword constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $type
	 * @param string $value
	 * @param string $label
	 * @param string $prepend
	 * @param string $append
	 * @param int $maxlength
	 * @param bool $required
	 * @param string $validation
	 * @param string $tip
	 * @param string $state
	 */
	public function __construct(string $id = '', string $name = '', string $class = '', string $value = '', string $label = '', $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false)
	{
		parent::__construct($id, $name, $class, 'password', $value, $label, '<i class="fas fa-unlock-alt"></i>', '<i class="fas fa-eye"></i><i class="fas fa-eye-slash"></i>', $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $prependIcon, $group, $optional, $readonly);

		$this->type = 'password';
	}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.input-password');
    }
}
