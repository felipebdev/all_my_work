<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateExpoPushTokenRequest;
use App\PlatformUser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ExpoPushToken extends Controller
{
    public function update(UpdateExpoPushTokenRequest $request)
    {
        $user = Auth::user();

        $affected = PlatformUser::where('id', $user->id)->update([
            'expo_push_token' => $request->expo_push_token,
        ]);

        if ($affected == 1) {
            return response()->noContent();
        }

        return response()->noContent(Response::HTTP_BAD_REQUEST);
    }
}
