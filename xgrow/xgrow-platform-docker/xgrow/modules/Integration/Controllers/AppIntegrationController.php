<?php

namespace Modules\Integration\Controllers;

use App\Http\Controllers\Controller;
use App\Integration as LegacyIntegration;
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

    public function newIndex()
    {
        return view('apps::integrations.new-index');
    }

    public function edit(Integration $integration, Request $request)
    {
        if ($request->ajax()) {
            return response()->json($integration);
        }
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

    public function destroy($integration)
    {
        try {
            if ($this->looksLikeUuid($integration)) {
                $integration = LegacyIntegration::where('id', $integration)->first();
                $integration->delete();
            } else {
                $integration = Integration::where('id', $integration)->first();
                $this->service->destroy($integration->id);
            }

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

    private function looksLikeUuid($uuid): bool
    {
        if (!is_string($uuid)) {
            return false;
        }

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) === 1) {
            return true;
        }

        return false;
    }
}
