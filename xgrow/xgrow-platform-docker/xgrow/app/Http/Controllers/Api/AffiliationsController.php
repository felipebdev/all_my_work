<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Auth\ClientStatus;
use Illuminate\Support\Facades\Auth;

class AffiliationsController extends Controller
{
    public function index()
    {
        $status = ClientStatus::withoutPlatform(Auth::user()->email);

        return view('affiliate-area.index', [
            'clientApproved' => $status->clientApproved,
        ]);
    }
}
