<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * Just for testing Vue components
         */
        $this->middleware('auth');
//        \Auth::loginUsingId(1);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function homeOne()
    {
        return view('home-one');
    }

    public function homeTwo()
    {
        return view('home-two');
    }
}
