<?php

namespace App\Http\Controllers;

use App\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LearningAreaController extends Controller
{
    public function index()
    {
        return view('learning-area.index');
    }

    public function generateTokenLA()
    {
        // Granted user is logged
        if (Auth::user()) {
            try {
                $key = config('app.lav3_key');
                $platform = Platform::where('id', '=', Auth::user()->platform_id)->first();
                $header = ['typ' => 'JWT', 'alg' => 'HS256'];
                $payload = [
                    'exp' => strtotime("+10 minutes"),
                    'platformId' => $platform->id,
                    'platformName' => $platform->name,
                    'userId' => Auth::user()->id,
                ];

                $header = json_encode($header);
                $header = base64_encode($header);

                $payload = json_encode($payload);
                $payload = base64_encode($payload);

                $sign = hash_hmac('sha256', $header . "." . $payload, $key, true);
                $sign = base64_encode($sign);

                $token = "$header.$payload.$sign";

                return response()->json([
                    'platformId' => $platform->id,
                    'token' => $token,
                    'url' => config('app.url_lav3')
                ]);

            } catch (\Exception $e) {
                Log::error('Erro ao criar token para LA. Error ' . $e->getMessage());
            }

        } else {
            return redirect()->route('login');
        }
    }
}
