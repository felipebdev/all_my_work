<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Client;
use App\Dashboard;
use App\Platform;
use Illuminate\Support\Facades\Auth;


class DeveloperController extends Controller
{
    public function index()
    {
        $isOwner = false;
        $client = Client::where('email', Auth::user()->email)->first();
        if ($client) {
            $platformOwner = Platform::where('customer_id', $client->id)
                ->where('id', Auth::user()->platform_id)->get();
            $isOwner = count($platformOwner) > 0;
        }

        if ($isOwner) {
            return view('developer.index');
        }

        return abort(404);
    }
}
