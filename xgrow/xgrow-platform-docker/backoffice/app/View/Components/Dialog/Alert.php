<?php

namespace App\View\Components\Dialog;

use App\View\Components\HTMLComponent;
use function view;

class Alert extends HTMLComponent
{
	/**
	 * @var string
	 */
	public string $type = 'warning';
	/**
	 * @var string|bool
	 */
	public string $visible = 'visible';

	/**
	 * Alert constructor.
	 *
	 * @param string|null $id
	 * @param string|null $name
	 * @param string $class
	 * @param string $type
	 * @param string $visible
	 */
    public function __construct(?string $id = '', ?string $name = '', string $class = '', string $type = 'warning', string $visible = 'visible')
    {
        parent::__construct('div', false, $id, $name, $class);

		$this->type = $type;
		$this->visible = $visible;
		$this->class .= ($visible !== 'visible' && $visible !== '1') ? 'hidden' : '';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dialog.alert');
    }
}
