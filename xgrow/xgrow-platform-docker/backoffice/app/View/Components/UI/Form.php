<?php

namespace App\View\Components\UI;

use App\View\Components\HTMLComponent;

/**
 * @class Form
 */
class Form extends HTMLComponent
{
	/**
	 * @var string
	 */
	public string $action;
	/**
	 * @var string
	 */
	public string $method;
	/**
	 * @var bool
	 */
	public bool $clearAfterCreate;
	/**
	 * @var bool
	 */
	public bool $clearAfterUpdate;
	/**
	 * @var string
	 */
	public string $feedBackType;

	/**
	 * Form constructor.
	 *
	 * @param string $action
	 * @param string $method
	 * @param string|null $id
	 * @param string|null $name
	 * @param string $class
	 * @param bool $clearAfterSuccess
	 * @param string $feedBackType
	 */
    public function __construct(string $action = '', string $method = 'post', ?string $id = '', ?string $name = '', string $class = '', string $feedBackType = 'modal', bool $clearAfterCreate = true, bool $clearAfterUpdate = false)
    {
		parent::__construct('form', false, $id, $name, $class);

        $this->action = $action;
		$this->method = $method;
		$this->clearAfterCreate = $clearAfterCreate;
		$this->clearAfterUpdate = $clearAfterUpdate;
		$this->feedBackType = $feedBackType;

		if($this->clearAfterCreate) $this->defaultAttributes['data-clear-after-create'] = 'data-clear-after-create';
		if($this->clearAfterUpdate) $this->defaultAttributes['data-clear-after-update'] = 'data-clear-after-update';

		//$this->defaultAttributes['data-clear-after-success'] = $this->clearAfterSuccess;
		$this->defaultAttributes['data-feed-back-type'] = $this->feedBackType;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.form');
    }
}
