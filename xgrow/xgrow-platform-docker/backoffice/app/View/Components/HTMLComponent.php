<?php

namespace App\View\Components;

use Illuminate\View\Component;

class HTMLComponent extends Component
{
	/**
	 * @var string|null
	 */
	public ?string $id;
	/**
	 * @var string|null
	 */
	public ?string $name;
	/**
	 * @var string
	 */
	public string $tag;
	/**
	 * @var string
	 */
	public string $class;
	/**
	 * @var bool
	 */
	public bool $selfClose = false;
	/**
	 * @var array
	 */
	public array $defaultAttributes = [];
	/**
	 * HTMLComponent constructor.
	 *
	 * @param string $tag
	 * @param bool $selfClose
	 * @param string|null $id
	 * @param string|null $name
	 * @param string $class
	 */
    public function __construct(string $tag, bool $selfClose = false, ?string $id = '', ?string $name = '', string $class = '')
    {
        $this->tag = $tag;
		$this->selfClose = $selfClose;
        $this->id = $id;
        $this->name = $name;
		$this->class = $class;

		if(!$this->name) $this->name = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.html-component');
    }
}
