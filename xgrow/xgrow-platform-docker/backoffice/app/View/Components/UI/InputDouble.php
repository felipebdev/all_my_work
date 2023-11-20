<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class InputDouble extends InputNumber
{
	/**
	 * @var string
	 */
	public string $punctuation = '';
	/**
	 * @var int
	 */
	public int $decimals = 2;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $id = '', string $name = '', string $class = '', string $value = '', string $label = '', $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false, string $punctuation = '', int $decimals = 2)
    {
	    parent::__construct($id, $name, $class, 'double', $value, $label, '', $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $prependIcon ?? '<i class="fas fa-sort-numeric-up"></i>', $group, $optional, $readonly);

	    $this->punctuation = $punctuation;
	    $this->decimals = $decimals;

		$this->defaultAttributes['data-punctuation'] = $punctuation;
		$this->defaultAttributes['data-decimals'] = $decimals;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.input-double');
    }
}
