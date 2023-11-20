<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Billsby extends Controller
{
    private $id;
    private $dados;
    public function index(Request $request,$uuid)
    {
        $this->id =  $uuid;
        $this->dados = $request->all();
        switch($this->dados['Type']){
            case 'CustomerCreated':
            break;
            case 'SubscriptionUpdated':
            break;
        }
    }
}
