<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Services\Actions\RunChargeRulerForNoLimitAction;
use App\Services\Actions\RunChargeRulerForSubscriptionsAction;
use Illuminate\Http\Request;

use function response;

class ChargeRulerController extends Controller
{


    public function subscription(Request $request)
    {
        $originalLimit = ini_get('max_execution_time');
        ini_set('max_execution_time', 0);

        $debugOptions = [
            'platform_id' => $request->input('platform_id') ?? null,
            'subscriber_id' => (int) $request->input('subscriber_id') ?? null,
            'payment_id' => (int) $request->input('payment_id') ?? null,
            'dry-run' => (bool) ($request->input('dry-run') ?? false),
            'base-date' => $request->input('base-date') ?? null,
        ];

        $action = new RunChargeRulerForSubscriptionsAction($debugOptions);
        $action();

        ini_set('max_execution_time', $originalLimit);

        return response()->noContent();
    }

    public function noLimit(Request $request)
    {
        $originalLimit = ini_get('max_execution_time');
        ini_set('max_execution_time', 0);

        $debugOptions = [
            'platform_id' => $request->input('platform_id') ?? null,
            'subscriber_id' => $request->input('subscriber_id') ?? null,
            'payment_id' => $request->input('payment_id') ?? null,
            'dry-run' => $request->input('dry-run') ?? false,
            'base-date' => $request->input('base-date') ?? null,
        ];

        $action = new RunChargeRulerForNoLimitAction($debugOptions);
        $action();

        ini_set('max_execution_time', $originalLimit);

        return response()->noContent();
    }


}
