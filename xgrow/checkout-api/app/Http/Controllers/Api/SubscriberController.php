<?php

namespace App\Http\Controllers\Api;

use App\Facades\JwtPlatformFacade;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubscriberController extends Controller
{
    public function userInfo()
    {
        try {
            return JwtPlatformFacade::getSubscriber();
        } catch (ModelNotFoundException $e) {
            return response()->json('Subscriber not found', 404);
        }
    }
}
