<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DOMComponent extends Component
{
	/**
	 * @var string|null
	 */
	public ?string $id = null;
	/**
	 * @var string|null
	 */
	public ?string $name = null;
	/**
	 * @var string
	 */
	public ?string $class = null;
	/**
	 * @var array|null
	 */
	public ?array $defaultAttributes = null;
	/**
	 * @var array|null
	 */
	public ?array $dataAttributes = null;
	/**
	 * DOMComponent constructor.
	 *
	 * @param string|null $id
	 * @param string|null $name
	 * @param string|null $class
	 * @param array|null $defaultAttributes
	 * @param array|null $dataAttributes
	 */
    public function __construct(?string $id = null, ?string $name = null, ?string $class = null, ?array $defaultAttributes = null, ?array $dataAttributes = null)
    {
        $this->id = $id;
		$this->name = $name;
		$this->class = $class;
		$this->defaultAttributes = $defaultAttributes;
		$this->dataAttributes = $dataAttributes;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dom-component');
    }
}
