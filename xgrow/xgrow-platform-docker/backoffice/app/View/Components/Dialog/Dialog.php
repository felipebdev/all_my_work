<?php

namespace App\View\Components\Dialog;

use Illuminate\View\Component;
use function view;

class Dialog extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?string $id = null, ?string $name = null, string $class = '')
    {

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dialog');
    }
}
