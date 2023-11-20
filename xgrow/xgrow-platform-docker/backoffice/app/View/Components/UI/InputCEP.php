<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class InputCEP extends Input
{
	/**
	 * @var string|null
	 */
	public ?string $fragment = null;
	/**
	 * @var string|null
	 */
	public ?string $cepInput = null;
	/**
	 * InputCEP constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $value
	 * @param string $label
	 * @param string $append
	 * @param int $maxlength
	 * @param bool $required
	 * @param string $validation
	 * @param string $tip
	 * @param string $state
	 * @param int $schema
	 * @param string|null $fragment
	 * @param string|null $cepInput
	 * @param string|null $prependIcon
	 */
	public function __construct(string $id = '', string $name = '', string $class = '', string $value = '', string $label = '', string $append = '', $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $fragment = null, ?string $cepInput = null, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false)
	{
		if(!isset($fragment) || empty($fragment))
		{
			$validation = 'cep';
			$maxlength = 10;
		}

		parent::__construct($id, $name, $class, 'cep', $value, $label, Input::getIcon($fragment, '<i class="fas fa-map-pin"></i>'), $append, $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $prependIcon, $group, $optional, $readonly);

		$this->defaultAttributes['data-cep-input'] = $cepInput;
		$this->defaultAttributes['data-fragment'] = $fragment;
	}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.input-cep');
    }
}
