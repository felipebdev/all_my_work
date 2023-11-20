<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Http\Requests\Integrations\StoreIntegrationRequest;
use App\Integration;
use App\IntegrationType;
use App\Plan;
use App\Services\PandaVideo\PandaVideoRequestService;
use Auth;
use DB;
use Exception;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IntegracaoController extends Controller
{

    private $integration;

    public function __construct(Integration $integration)
    {
        $this->integration = $integration;
    }

    public function index()
    {
        $data = [];

        $webhook = DB::table('integrations')
            ->join('platforms', 'integrations.platform_id', '=', 'platforms.id')
            ->where('platforms.id', '=', Auth::user()->platform_id)
            ->select('integrations.id AS integration_id', 'integrations.id_webhook', 'integrations.id_integration', 'integrations.name_integration', 'integrations.source_token', 'integrations.flag_enable', 'integrations.url_webhook', 'integrations.days_limit_payment_pendent', 'platforms.*')
            ->orderBy('integrations.id_integration', 'ASC')
            ->get();

        $data["webhooks"] = $webhook;

        return view('integracao.index', $data);
    }

    public function logs()
    {
        $data = [];

        $client = new \GuzzleHttp\Client();

        try {
            $apiRequest = $client->request('GET', 'http://localhost:3001/integration-logs/' . Auth::user()->platform_id, [
                'headers' => [
                    'Authorization' => "YTI5Y2EyMDNlYTAxNWU0YjBlNzQxNzI1YmVkY2I3ZjNjYTA2ODcyN2IxNTc5NGYyNThiYjVhMGI4NTZhNmYxNmRkODU5ZGFi"
                ]
            ]);

            $response = json_decode($apiRequest->getBody());

            $data["integracaologs"] = $response->data;

            return view('integracao-logs.index', $data);

        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

    }

    public function logsErrors()
    {
        $data = [];

        $client = new \GuzzleHttp\Client();

        try {
            $apiRequest = $client->request('GET', 'http://localhost:3001/integration-errors/' . Auth::user()->platform_id, [
                'headers' => [
                    'Authorization' => "YTI5Y2EyMDNlYTAxNWU0YjBlNzQxNzI1YmVkY2I3ZjNjYTA2ODcyN2IxNTc5NGYyNThiYjVhMGI4NTZhNmYxNmRkODU5ZGFi"
                ]
            ]);

            $response = json_decode($apiRequest->getBody());

            $data["integracaologs"] = $response->data;

            return view('integracao-logs.index', $data);

        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

    }

    public function logsDetailsErrors($id)
    {
        $data = [];

        $client = new \GuzzleHttp\Client();

        try {
            $apiRequest = $client->request('GET', 'http://localhost:3001/integration-one-log-error/' . Auth::user()->platform_id . '/' . $id, [
                'headers' => [
                    'Authorization' => "YTI5Y2EyMDNlYTAxNWU0YjBlNzQxNzI1YmVkY2I3ZjNjYTA2ODcyN2IxNTc5NGYyNThiYjVhMGI4NTZhNmYxNmRkODU5ZGFi"
                ]
            ]);

            $response = json_decode($apiRequest->getBody());

            $data["details"] = $response->data;

            return view('integracao-logs.details', $data);

        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

    }

    public function logsDetails($id)
    {
        $data = [];

        $client = new \GuzzleHttp\Client();

        try {
            $apiRequest = $client->request('GET', 'http://localhost:3001/integration-one-log/' . Auth::user()->platform_id . '/' . $id, [
                'headers' => [
                    'Authorization' => "YTI5Y2EyMDNlYTAxNWU0YjBlNzQxNzI1YmVkY2I3ZjNjYTA2ODcyN2IxNTc5NGYyNThiYjVhMGI4NTZhNmYxNmRkODU5ZGFi"
                ]
            ]);

            $response = json_decode($apiRequest->getBody());

            $data["details"] = $response->data;

            return view('integracao-logs.details', $data);

        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

    }

    public function create()
    {
        $data['providers'] = Integration::integrations();
        return view('integracao.create', $data);
    }

    public function store(StoreIntegrationRequest $request)
    {
        try {
            $integration = $this->integration;

            $uuid = Str::uuid();
            $integration->id = $uuid;

            switch ($request->id_integration) {
                case 1:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_HOTMART;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_HOTMART);
                    break;
                case 2:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_SUPELOGICA;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_SUPELOGICA);
                    break;
                case 3:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_BILLSBY;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_BILLSBY);
                    break;
                case 4:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_GETNET;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_GETNET);
                    $this->prepareTokenGetnet($request);
                    break;
                case 5:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_MUNDIPAGG;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_MUNDIPAGG);
                    $this->prepareTokenMundipagg($request);
                    break;
                case 6:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_EDUZZ;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_EDUZZ);
                    break;
                case 7:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_PLX;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_PLX);
                    break;
                case 8:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_ACTIVECAMPAIGN;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_ACTIVECAMPAIGN);
                    $this->prepareFieldsActiveCampaign($request);
                    break;
                case 9:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_FACEBOOKPIXEL;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_FACEBOOKPIXEL);
                    $this->prepareFieldsFacebookPixel($request);
                    break;
                case 10:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_GOOGLEADS;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_GOOGLEADS);
                    $this->prepareFieldsGoogleAds($request);
                    break;
                case 11:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_SMARTNOTAS;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_SMARTNOTAS);
                    $this->prepareFieldsSmartNotas($request);
                    break;
                case 12:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_OCTADESK;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_OCTADESK);
                    $this->prepareFieldsOctadesk($request);
                    break;
                case 13:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_DIGITALMANAGERGURU;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_DIGITALMANAGERGURU);
                    $this->prepareFieldsOctadesk($request);
                    break;
                case 14:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_KAJABI;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_KAJABI);
                    $this->prepareFieldsKajabi($request);
                    break;
                case 15:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_CADEMI;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_CADEMI);
                    $this->prepareFieldsCademi($request);
                    break;
                case 16:
                    $integration->id_integration = Constants::CONSTANT_INTEGRATION_PANDAVIDEO;
                    $script = strtolower(Constants::CONSTANT_INTEGRATION_PANDAVIDEO);
                    $this->prepareFieldsPandaVideo($request);
                    break;
            }


            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_DIGITALMANAGERGURU)) {
                $this->prepareFieldsGuruManager($request);
            }

            $flag_enable = (isset($request->flag_enable)) ? 1 : 0;

            $platform = Auth::user()->platform_id;
            $integration->platform_id = $platform;
            $integration->name_integration = $request->name_integration;
            $integration->source_token = $request->source_token;
            $integration->flag_enable = $flag_enable;
            $integration->id_webhook = (int)$request->id_integration;
            $integration->url_webhook = ($request->url_webhook) ? $request->url_webhook : config('app.url') . '/api/' . $script . '/' . $uuid;
            $integration->days_limit_payment_pendent = (int)$request->days_limit_payment_pendent;
            $integration->trigger_email = $request->trigger_email ?? 0;

            $integration->save();

            return redirect()->route('apps.integrations.index')
                ->with('success', 'Integração cadastrada com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Um erro ocorreu e não foi possível cadastrar a integração');
        }
    }

    public function edit($id, Request $request)
    {
        $webhook = $this->integration->findOrFail($id);

        if ($webhook->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_GETNET)) {
            $webhook = $this->prepareFieldsGetnet($webhook);
        }

        if ($webhook->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG)) {
            $webhook = $this->prepareFieldsMundipagg($webhook);
        }

        if ($request->ajax()) {
            return response()->json($webhook, 200);
        }

        return view('integracao.edit', compact('webhook'));
    }

    public function update(StoreIntegrationRequest $request, $id)
    {
        try {
            $integration = $this->integration->findOrFail($id);
            $integration->name_integration = $request->name_integration;
            $integration->flag_enable = (int)$request->flag_enable;
            $integration->days_limit_payment_pendent = (int)$request->days_limit_payment_pendent;

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_GETNET)) {
                $this->prepareTokenGetnet($request);
            }

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG)) {
                $this->prepareTokenMundipagg($request);
            }

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_ACTIVECAMPAIGN)) {
                $this->prepareFieldsActiveCampaign($request);
            }

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_FACEBOOKPIXEL)) {
                $this->prepareFieldsFacebookPixel($request);
            }

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_GOOGLEADS)) {
                $this->prepareFieldsGoogleAds($request);
            }

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_SMARTNOTAS)) {
                $this->prepareFieldsSmartNotas($request);
            }

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_OCTADESK)) {
                $this->prepareFieldsOctadesk($request);
            }

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_DIGITALMANAGERGURU)) {
                $this->prepareFieldsGuruManager($request);
            }

            if ((int)$request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_KAJABI)) {
                $this->prepareFieldsKajabi($request);
            }

            if ((int) $request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_CADEMI)) {
                $this->prepareFieldsCademi($request);
            }

            if ((int) $request->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_PANDAVIDEO)) {
                $this->prepareFieldsPandaVideo($request);
            }

            $integration->source_token = $request->source_token;
            $integration->url_webhook = $request->url_webhook;

            $flag_enable = (isset($request->flag_enable)) ? 1 : 0;
            $integration->flag_enable = $flag_enable;

            $flagTriggerEmail = (isset($request->trigger_email)) ? 1 : 0;

            if ($integration->trigger_email !== $flagTriggerEmail) {
                $this->alterTriggerEmailPlans($integration, $flagTriggerEmail);
            }

            $integration->trigger_email = $flagTriggerEmail;

            $integration->save();

            return redirect()->route('apps.integrations.index')
                ->with('success', 'Integração atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = $this->integration->findOrFail($id);
            $user->delete();

            return redirect()->route('apps.integrations.index')
                ->with('success', 'Integração excluída com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Um erro ocorreu e não foi possível excluir a integração');
        }
    }

    public function prepareTokenGetnet($request)
    {
        $token = [
            'local' => [
                'seller_id' => $request->homol_seller_id,
                'client_id' => $request->homol_client_id,
                'secret_id' => $request->homol_secret_id,
                'url_api' => $request->homol_url_api,
                'url_checkout' => $request->homol_url_checkout
            ],
            'production' => [
                'seller_id' => $request->prod_seller_id,
                'client_id' => $request->prod_client_id,
                'secret_id' => $request->prod_secret_id,
                'url_api' => $request->prod_url_api,
                'url_checkout' => $request->prod_url_checkout
            ]
        ];

        $request->request->add(['source_token' => json_encode($token)]);
    }

    public function prepareFieldsGetnet($webhook)
    {
        $webhook->tokensGetnet = json_decode($webhook->source_token, false);

        return $webhook;
    }

    public function prepareFieldsGuruManager($request)
    {
        $input = array('api_key' => $request->digitalmanagerguru_api_key);
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function prepareFieldsActiveCampaign($request)
    {
        $input = array_merge(
            ['api_key' => $request->activecampaign_api_key],
            $request->events ?? []
        );
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function prepareFieldsFacebookPixel($request)
    {
        $input = array_merge([
            'pixel_id' => $request->pixel_id,
            'pixel_token' => $request->pixel_token,
            'pixel_test_event_code' => $request->pixel_test_event_code,
        ], $request->infos ?? []);
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function prepareFieldsGoogleAds($request)
    {
        $input = array_merge(['adwords_id' => $request->ads_id], $request->infos ?? []);
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function prepareFieldsSmartNotas($request)
    {
        $input = array_merge(
            ['api_key' => Auth::user()->platform_id, 'process_after_days' => $request->process_after_days],
            $request->events ?? []
        );
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function prepareFieldsOctadesk($request)
    {
        $input = array_merge(
            ['api_key' => $request->api_key, 'email_client' => $request->email_client],
            $request->events ?? []
        );
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function prepareFieldsKajabi($request)
    {
        $input = array_merge(
            ['email_client' => $request->email_client],
            $request->events ?? []
        );
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function prepareFieldsPandaVideo($request)
    {
        $input = array(
            'api_key' => $request->pandavideo_api_key
        );
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function prepareFieldsCademi($request)
    {
        $input = array_merge(
            ['api_key' => $request->api_key],
            $request->events ?? []
        );
        $request->request->add(['source_token' => json_encode($input)]);
    }

    public function status($id)
    {
        $integration = Integration::where('platform_id', '=', Auth::user()->platform_id)->where('id_webhook', '=', $id)->first();

        DB::table('integrations')
            ->where('id_webhook', $id)
            ->where('platform_id', '=', Auth::user()->platform_id)
            ->update(['flag_enable' => (int)!$integration->flag_enable]);

        return response()->json([
            'status' => $integration->flag_enable ? 'Ativo' : 'Inativo'
        ]);
    }

    public function prepareTokenMundipagg($request)
    {
        $token = [
            'local' => [
                'count_id' => $request->homol_count_id,
                'public_key' => $request->homol_public_key,
                'secret_key' => $request->homol_secret_key
            ],
            'production' => [
                'count_id' => $request->prod_count_id,
                'public_key' => $request->prod_public_key,
                'secret_key' => $request->prod_secret_key
            ]
        ];

        $request->request->add(['source_token' => json_encode($token)]);
    }

    public function prepareFieldsMundipagg($webhook)
    {
        $webhook->tokensMundipagg = json_decode($webhook->source_token, false);

        return $webhook;
    }

    private function alterTriggerEmailPlans($integration, $flagTriggerEmail)
    {
        $integrationType = IntegrationType::whereIntegrationId($integration->id)->pluck('integratable_id')->toArray();

        $plans = Plan::whereIn('id', $integrationType)->whereTriggerEmail($integration->trigger_email)->get();

        if ($plans !== null) {
            foreach ($plans as $plan) {
                $plan->trigger_email = $flagTriggerEmail;
                $plan->save();
            }
        }
        return true;
    }

    public function getActiveCampaignLists(Request $request)
    {
        $url = $request->apiUrl;
        $apiKey = $request->apiKey;

        if (empty($url) || empty($apiKey)) {
            return response()->json([], 400);
        }

        try {
            $client = new HttpClient();
            $apiRequest = $client->request('GET', "${url}/api/3/lists", [
                'headers' => [
                    'Api-Token' => $apiKey
                ]
            ]);

            $response = json_decode($apiRequest->getBody()) ?? [];
            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getActiveCampaignTags(Request $request)
    {
        $url = $request->apiUrl;
        $apiKey = $request->apiKey;

        if (empty($url) || empty($apiKey)) {
            return response()->json([], 400);
        }

        try {
            $client = new HttpClient();
            $apiRequest = $client->request('GET', "${url}/api/3/tags", [
                'headers' => [
                    'Api-Token' => $apiKey
                ]
            ]);

            $response = json_decode($apiRequest->getBody()) ?? [];
            return response()->json($response, 200);

        } catch (Exception $e) {
            return response()->json([], 500);
        }
    }

    //Endpoint de teste para integração
    public function teste(Request $request)
    {
        $pandaVideoService = new PandaVideoRequestService();
//        $res = $pandaVideoService->deleteFolder('a7173495-b402-4382-abf7-b533446adfae');
//
//        if ($request->method() == 'POST') {
//            $video = $request->file('pandavideo');
//            $file_path = $video->getPathname();
//            $file_mime = $video->getMimeType('video');
//            $file_uploaded_name = $video->getClientOriginalName();
//
//            if ($video == null) {
//                return back()->with('error', 'Video não pode ficar em branco.');
//            }
//
//            $res = $pandaVideoService->uploadVideo([
//                'multipart' => [
//                    'name' => 'upload_file',
//                    'filename' => $file_uploaded_name,
//                    'Mime-Type' => $file_mime,
//                    'contents' => fopen($file_path, 'r'),
//                ]
//            ]);
//            dd(json_decode($res));
//        }
//
//        return view('integracao.old.teste');
//        $videos = $pandaVideoService->getVideos();
//        echo $videos->getBody();
    }
}
