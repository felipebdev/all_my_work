<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\IntegrationAction;
use DB;
use Auth;

class IntegracaoActionController extends Controller
{

    private $integrationAction;

    public function __construct(IntegrationAction $integrationAction)
    {
        $this->integrationAction = $integrationAction;
    }

    public static function index($integration_id)
    {
        $data = [];

        $actions = DB::table('integrations_actions')
            ->join('platforms', 'integrations_actions.platform_id', '=', 'platforms.id')
            ->join('integrations', 'integrations_actions.integration_id', '=', 'integrations.id')
            ->join('integrations_actions_list', 'integrations_actions.integrations_actions_list_id', '=', 'integrations_actions_list.id')
            ->where('platforms.id', '=', Auth::user()->platform_id)
            ->where('integrations.id', '=', $integration_id)
            ->select(
                'integrations_actions.id AS action_id',
                'integrations_actions.description AS description',
                'integrations_actions.status AS status',
                'integrations_actions.trigger AS trigger',
                'integrations_actions.extra AS extra',
                'integrations_actions_list.name AS action',
                'platforms.*'
            )
            ->get();

        $data["actions"] = $actions;

        return $data;
    }

    public static function getActiveCampaingList($integration_id)
    {
        $webhook = DB::table('integrations')
            ->join('platforms', 'integrations.platform_id', '=', 'platforms.id')
            ->where('integrations.id', '=', $integration_id)
            ->select('integrations.id AS integration_id', 'integrations.id_webhook', 'integrations.id_integration', 'integrations.name_integration', 'integrations.source_token', 'integrations.flag_enable', 'integrations.url_webhook', 'integrations.days_limit_payment_pendent', 'platforms.*')
            ->first();

        $client = new \GuzzleHttp\Client();

        $url = $webhook->url_webhook;

        try {
            $apiRequest = $client->request('GET', $url . '/api/3/lists', [
                'headers' => [
                    'Api-Token' => $webhook->source_token
                ]
            ]);

            $response = json_decode($apiRequest->getBody());
        return $response;

        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function create(Request $request, $id)
    {
        $integrationAction = $this->integrationAction;

        $integrationAction->description = $request->description;
        $integrationAction->trigger = $request->trigger;
        $integrationAction->platform_id = Auth::user()->platform_id;
        $integrationAction->integrations_actions_list_id = $request->action;
        $integrationAction->integration_id = $id;
        $integrationAction->extra = !empty($request->tags) ? $request->tags : $request->listCtt ;

        $integrationAction->save();

        return redirect()->route('integracao.edit', [$id]);
    }

    public function update(Request $request, $id)
    {
    }

    public function updateStatus(Request $request, $id, $webhookId)
    {
        try {
            $integration = $this->integrationAction->findOrFail($id);
            $integration->status = !empty($request->status) ? 'active' : 'desactive';
            $integration->save();
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
        return redirect()->route('integracao.edit', [$webhookId])->with(['message' => "Status atualizado com sucesso!"]);
    }

    public function destroy($id, $webhookId)
    {
        $integration = $this->integrationAction->findOrFail($id);
        $integration->delete();
        return redirect()->route('integracao.edit', [$webhookId])->with(['message' => "Ação apagada com sucesso!"]);
    }
}
