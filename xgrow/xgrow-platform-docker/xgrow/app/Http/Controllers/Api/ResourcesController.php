<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourcesController extends Controller
{
    public function index(Request $request)
    {
        return view('mobile.index');
    }
}
