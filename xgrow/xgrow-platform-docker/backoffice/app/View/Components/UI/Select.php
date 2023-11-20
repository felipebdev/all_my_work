<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

/**
 * @class {Select}
 */
class Select extends Input
{
	/**
	 * @var string
	 */
	public string $optionsString = '';
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
	public bool $multiple = false;
	/**
	 * @var array
	 */
	public array $options = [];
	/**
	 * Select constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $type
	 * @param string $value
	 * @param string $label
	 * @param string|null $prepend
	 * @param string|null $append
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
	 * @param string $valueIdentifier
	 * @param string $labelIdentifier
	 * @param bool $multiple
	 * @param array $options
	 */
    public function __construct(string $id = '', string $name = '', string $class = '', string $type = 'select', string $value = '', string $label = '', ?string $prepend = null, ?string $append = null, $maxlength = 0, bool $required = false, ?string $validation = null, ?string $serverValidation = null, string $tip = '', string $state = 'idle', $schema = -1, ?string $prependIcon = null, string $group = '', ?string $optional = null, bool $readonly = false, string $valueIdentifier = '', string $labelIdentifier = '', bool $multiple = false, array $options = [])
    {
	    parent::__construct($id, $name, $class, $type, $value, $label, $prepend, $append, $maxlength, $required, $validation, $serverValidation, $tip, $state, $schema, $prependIcon, $group, $optional, $readonly);

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
        return view('components.ui.select');
    }
}
