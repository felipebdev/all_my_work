<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LACustomizationController extends Controller
{
    public function visualIdentity()
    {
        $data['apiUrl'] = env('LA_PLATFORM_CONFIGURATION_API');
        $data['platformId'] = Auth::user()->platform_id;
        $data['userId'] = Auth::user()->id;

        return view('la-customization.visual-identity.index', $data);
    }
}
