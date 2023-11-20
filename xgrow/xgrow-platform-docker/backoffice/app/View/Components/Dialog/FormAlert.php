<?php

namespace App\View\Components\Dialog;

use App\View\Components\HTMLComponent;
use function view;

class FormAlert extends HTMLComponent
{
	/**
	 * @var string
	 */
	public string $params;
	/**
	 * @var string
	 */
	public string $fixed;
	/**
	 * @var string
	 */
	public string $type;
	/**
	 * FormAlert constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $type
	 * @param string $fixed
	 */
    public function __construct(string $id = '', string $name = '', string $class = '', string $type = 'info', string $fixed = 'fixed', string $params = '')
    {
	    parent::__construct('li', false, $id, $name, $class);

		$this->type = $type;
		$this->fixed = $fixed;
		$this->params = $params;
		$this->defaultAttributes['data-fixed'] = $fixed;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dialog.form-alert');
    }
}
