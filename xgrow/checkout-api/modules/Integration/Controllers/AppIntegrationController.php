<?php

namespace Modules\Integration\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Modules\Integration\Contracts\IAppIntegrationService;
use Modules\Integration\Models\Integration;
use Modules\Integration\Requests\StoreIntegrationRequest;

class AppIntegrationController extends Controller
{
    private $service;

    public function __construct(IAppIntegrationService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $apps = $this->service->all(Auth::user()->platform_id);
        return view('apps::integrations.index', compact('apps'));
    }

    public function show(Integration $integration, Request $request)
    {
        if ($request->ajax()) {
            return response()->json($integration);
        }
    }

    public function store(StoreIntegrationRequest $request)
    {
        try {
            $this->service->store($request->all());

            return redirect()->route('apps.integrations.index')
                ->with('success', 'Integração cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Um erro ocorreu e não foi possível cadastrar a integração.');
        }
    }

    public function update(Integration $integration, StoreIntegrationRequest $request)
    {
        try {
            $this->service->update(
                $integration->id,
                $request->all()
            );

            return redirect()->route('apps.integrations.index')
                ->with('success', 'Integração atualizada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Um erro ocorreu e não foi possível atualizar a integração.');
        }
    }

    public function destroy(Integration $integration)
    {
        try {
            $this->service->destroy($integration->id);

            return redirect()->route('apps.integrations.index')
                ->with('success', 'Integração excluída com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Um erro ocorreu e não foi possível excluir a integração.');
        }
    }

    public function metadata(
        Integration $integration, 
        string $provider,
        string $resource,
        Request $request
    ) {
        try {
            $provider = Str::ucfirst($provider);
            $providerClass = "Modules\\Integration\\Providers\\{$provider}";
            $provider = (new $providerClass())->build($integration);
            $data = $provider->{$resource}($request->all());

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json([], 500);
        }
    }
}
