<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class AffiliatesController extends Controller
{
    public function index()
    {
        return view('affiliates.index');
    }
}
