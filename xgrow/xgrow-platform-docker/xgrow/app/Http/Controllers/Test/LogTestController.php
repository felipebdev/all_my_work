<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Logs\ChargeLog;
use App\Logs\XgrowLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogTestController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::emergency('Log emergency test', ['request' => $request->all()]);
        Log::alert('Log alert test', ['request' => $request->all()]);
        Log::critical('Log critical test', ['request' => $request->all()]);
        Log::error('Log error test', ['request' => $request->all()]);
        Log::warning('Log warning test', ['request' => $request->all()]);
        Log::notice('Log notice test', ['request' => $request->all()]);
        Log::info('Log info test', ['request' => $request->all()]);
        Log::debug('Log debug test', ['request' => $request->all()]);

        ChargeLog::emergency('ChargeLog emergency test', ['request' => $request->all()]);
        ChargeLog::alert('ChargeLog alert test', ['request' => $request->all()]);
        ChargeLog::critical('ChargeLog critical test', ['request' => $request->all()]);
        ChargeLog::error('ChargeLog error test', ['request' => $request->all()]);
        ChargeLog::warning('ChargeLog warning test', ['request' => $request->all()]);
        ChargeLog::notice('ChargeLog notice test', ['request' => $request->all()]);
        ChargeLog::info('ChargeLog info test', ['request' => $request->all()]);
        ChargeLog::debug('ChargeLog debug test', ['request' => $request->all()]);

        XgrowLog::mail()->emergency('XgrowLog mail emergency test', ['request' => $request->all()]);
        XgrowLog::mail()->alert('XgrowLog mail alert test', ['request' => $request->all()]);
        XgrowLog::mail()->critical('XgrowLog mail critical test', ['request' => $request->all()]);
        XgrowLog::mail()->error('XgrowLog mail error test', ['request' => $request->all()]);
        XgrowLog::mail()->warning('XgrowLog mail warning test', ['request' => $request->all()]);
        XgrowLog::mail()->notice('XgrowLog mail notice test', ['request' => $request->all()]);
        XgrowLog::mail()->info('XgrowLog mail info test', ['request' => $request->all()]);
        XgrowLog::mail()->debug('XgrowLog mail debug test', ['request' => $request->all()]);

        XgrowLog::xInfo('XgrowLog xInfo test', ['request' => $request->all()]);
    }
}
