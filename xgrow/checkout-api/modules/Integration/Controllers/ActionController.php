<?php

namespace Modules\Integration\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\Integration\Contracts\IActionService;
use Modules\Integration\Models\Action;
use Modules\Integration\Models\Integration;
use Modules\Integration\Requests\StoreActionRequest;

class ActionController extends Controller
{
    /**
     * @var IActionService
     */
    private $service;

    public function __construct(IActionService $service)
    {
        $this->service = $service;
    }

    public function index(Integration $integration)
    {
        $actions = $this->service->allByIntegration($integration);
        return view('apps::actions.index', compact('actions', 'integration'));
    }

    public function show(
        Integration $integration,
        Action $action,
        Request $request
    ) {
        if ($request->ajax()) {
            $action->plans = $action->plans()->select('id')->get()->pluck('id');
            return response()->json($action);
        }
    }

    public function store(
        Integration $integration, 
        StoreActionRequest $request
    ) {
        try {
            $this->service->store(
                $integration,
                $request->all()
            );
            
            return redirect()
                ->route('apps.integrations.actions.index', ['integration' => $integration->id])
                ->with('success', 'Ação cadastrada com sucesso!');
        } catch(Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Um erro ocorreu e não foi possível cadastrar a ação.');
        }   
    }

    public function update(
        Integration $integration, 
        Action $action, 
        StoreActionRequest $request
    ) {
        try {
            $this->service->update(
                $action->id,
                $request->all()
            );
            
            return redirect()
                ->route('apps.integrations.actions.index', ['integration' => $integration->id])
                ->with('success', 'Ação atualizada com sucesso!');
        } catch(Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Um erro ocorreu e não foi possível atualizar a ação.');
        }
    }

    public function destroy(
        Integration $integration, 
        Action $action
    ) {
        try {
            $this->service->destroy($action->id);
            
            return redirect()
                ->route('apps.integrations.actions.index', ['integration' => $integration->id])
                ->with('success', 'Ação foi removida com sucesso!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Um erro ocorreu e não foi possível remover a ação.');
        }
    }
}
