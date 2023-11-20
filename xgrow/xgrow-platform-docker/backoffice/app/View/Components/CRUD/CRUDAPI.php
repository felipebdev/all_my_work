<?php

namespace App\View\Components\CRUD;

use Illuminate\View\Component;
use App\View\Components\DOMComponent;

class CRUDAPI
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.crud.crud-api');
    }
}
