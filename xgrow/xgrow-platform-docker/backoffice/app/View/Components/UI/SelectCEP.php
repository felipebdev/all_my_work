<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class SelectCEP extends InputCEP
{
	/**
	 * @var string
	 */
	public string $valueIdentifier = '';
	/**
	 * @var string
	 */
	public string $labelIdentifier = '';
	/**
	 * @var bool
	 */
	public  bool $multiple = false;
	/**
	 * @var array
	 */
	public array $options = [];
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $id = '', string $name = '', string $class = '', string $value = '', string $label = '', string $append = '', $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $fragment = null, ?string $cepInput = null, ?string $prependIcon = null, string $valueIdentifier = '', string $labelIdentifier = '', bool $multiple = false, array $options = [])
    {
		parent::__construct($id, $name, $class,  $value, $label, '', $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $fragment, $cepInput, $prependIcon);

	    $this->labelIdentifier = $labelIdentifier;
	    $this->valueIdentifier = $valueIdentifier;
	    $this->multiple = $multiple;
	    $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.select-cep');
    }
}
