<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TestEmail;
use Mail;

class TestEmailController extends Controller
{

    public function index()
    {
        $data = ['message' => 'This is a test!'];

    	Mail::to('fabio.fapeli@gmail.com')->send(new TestEmail($data));

    	/*
    	$user = new stdClass;
    	$user->name = 'Fabio Fapeli';
    	$user->email = 'fabio.fapeli@gmail.com';
        Mail::send('emails.test', ['user' => $user], function ($m) use ($user) {
			$m->from('contato@codecommerce.com', 'CodeCommerce');
			$m->to($user->email, $user->name)->subject('Dados de seu pedido!');
		});
		*/
    }

}
