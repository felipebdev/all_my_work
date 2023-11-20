<?php

namespace Modules\Integration\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Integration\Contracts\ILogService;
use Modules\Integration\Models\Integration;
use Modules\Integration\Queue\Jobs\RefreshIntegration;

class LogController extends Controller
{
    /**
     * @var Modules\Integration\Contracts\ILogService
     */
    private $service;

    public function __construct(ILogService $service)
    {
        $this->service = $service;
    }

    public function index(Integration $integration, Request $request)
    {
        if ($request->ajax()) {
            $logs = $this->service->paginate($integration);
            return datatables()
                ->collection($logs)
                ->toJson();
        }

        return abort(404);
    }

    public function show(Integration $integration, $id, Request $request)
    {
        if ($request->ajax()) {
            $log = $this->service->find($id);
            return response()->json($log);
        }

        return abort(404);
    }

    public function reprocess(Integration $integration, $id, Request $request)
    {   
        if ($request->ajax()) {
            $log = $this->service->find($id);
            if (is_null($log)) return response()->json([], 404);

            $actionId = $log['metadata']['action_id'] ?? null;
            $data = $log['request']['payload'] ?? null;

            if (is_null($actionId) || is_null($data)) return response()->json([], 404);

            RefreshIntegration::dispatchNow((int) $actionId, $data);
            return response()->json([], 200);
        }

        return abort(404);
    }
}
