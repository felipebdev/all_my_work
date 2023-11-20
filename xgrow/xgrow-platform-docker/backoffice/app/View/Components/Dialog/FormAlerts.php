<?php

namespace App\View\Components\Dialog;

use App\View\Components\HTMLComponent;
use function view;

class FormAlerts extends HTMLComponent
{
	/**
	 * @var string
	 */
	public string $title;
	/**
	 * @var string
	 */
	public string $target;
	/**
	 * FormAlerts constructor.
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $class
	 * @param string $title
	 * @param string $target
	 */
    public function __construct(string $id = '', string $name = '', string $class = '', string $title = '', string $target = '')
    {
        parent::__construct('div', false, $id, $name, $class);

		$this->title = $title;
		$this->target = $target;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dialog.form-alerts');
    }
}
