<?php

namespace Modules\Integration\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Str;
use Modules\Integration\Contracts\IOAuthService;
use Modules\Integration\Requests\StoreOAuthRequest;

class OAuthController extends Controller
{
    /**
     * @var Modules\Integration\Contracts\IOAuthService
     */
    private $service;

    public function __construct(IOAuthService $oAuthService)
    {
        $this->service = $oAuthService;
    }

    public function callback(StoreOAuthRequest $request) {
        try {
            //state url pattern = <platform_id>#<provider_name>
            list($platformId, $providerName) = explode('#', base64_decode($request->state));
            $providerName = Str::ucfirst($providerName);
            $providerClass =  "\Modules\Integration\Providers\\{$providerName}";
            $provider = new $providerClass();
            $this->service->save($provider, $platformId, $request->code);

            return redirect()->route('apps.integrations.index')
                ->with('success', 'Integração cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('apps.integrations.index')
                ->with('error', 'Um erro ocorreu e não foi possível cadastrar a integração.');
        }
    }
}
