<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

class InputCurrency extends Input
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $id = '', string $name = '', string $class = '', string $value = '', string $label = '', string $append = '', $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false)
    {
	    parent::__construct($id, $name, $class, 'currency', $value, $label, '<i class="far fa-money-bill-alt"></i>', $append, $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $prependIcon, $group, $optional, $readonly);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.input-currency');
    }
}
