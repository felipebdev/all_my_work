<?php

namespace App\Http\Controllers;

use App\Plan;
use App\IntegrationType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\IntegrationActionList;
use DB;
use Auth;
use App\Constants;

class IntegracaoActionListController extends Controller
{

    private $integrationActionList;

    public function __construct(IntegrationActionList $integrationActionList)
    {
        $this->integrationActionList = $integrationActionList;
    }

    public static function index()
    {
        $data = [];

        $actionsList = DB::table('integrations_actions_list')
            ->select('integrations_actions_list.*')
            ->get();

        $data["actionsList"] = $actionsList;

        return $data;
    }

    public function create()
    {
        $data['providers'] = Integration::integrations();
        return view('integracao.create', $data);
    }

    public function store(Request $request)
    {

        return redirect()->route('integracao.edit', [$integration->id]);
    }

    public function edit($id)
    {

        return view('integracao.edit',compact('webhook'));
    }

    public function update(Request $request, $id)
    {
        try {


        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

        return redirect()->route('integracao.index')->with(['message' => "Dados atualizados com sucesso!"]);
    }

    public function destroy($id)
    {
        $user = $this->integration->findOrFail($id);
        $user->delete();
        return redirect()->route('integracao.index');
    }
}
