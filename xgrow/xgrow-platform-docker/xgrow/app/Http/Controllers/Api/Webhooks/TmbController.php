<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\Tmb\TmbJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TmbController extends Controller
{

    public function __invoke(Request $request)
    {
        $email = $request->email;

        if (!validateEmail($email)) {
            Log::warning('TMB: invalid email', [
                'request' => $request->all(),
            ]);

            return response()->json('invalid email', 400);
        }

        TmbJob::dispatch($request->all());

        return response()->noContent(201);
    }
}
