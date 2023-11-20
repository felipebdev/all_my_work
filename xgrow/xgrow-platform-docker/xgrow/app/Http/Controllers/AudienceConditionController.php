<?php

namespace App\Http\Controllers;

use App\Jobs\AudienceExportJob;
use App\Repositories\Campaign\AudienceConditionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AudienceConditionController extends Controller
{
    public function datatables(Request $request, AudienceConditionRepository $repo)
    {
        $platformId = Auth::user()->platform_id;
        $conditions = $request->conditions ?? [];
        $query = $repo->generateQueryFromArray($platformId, $conditions);
        return datatables()->eloquent($query)->toJson();
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $platformId = $user->platform_id;
        $conditions = $request->conditions ?? [];
        AudienceExportJob::dispatch($user->id, $platformId, $conditions, 'csv');
        return response()->noContent();
    }

    public function exportXlsx(Request $request)
    {
        $user = Auth::user();
        $platformId = $user->platform_id;
        $conditions = $request->conditions ?? [];
        AudienceExportJob::dispatch($user->id, $platformId, $conditions, 'xlsx');
        return response()->noContent();
    }
}
