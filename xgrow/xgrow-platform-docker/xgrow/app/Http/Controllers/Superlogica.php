<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Superlogica extends Controller
{
    private $id;
    private $dados;
    public function index(Request $request,$uuid)
    {
        $this->id =  $uuid;
        $this->dados = $request->all();
        
       
    }
}
