<?php

namespace App\Http\Controllers;

use App\Audience;
use App\AudienceActions;
use App\AudienceCondition;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AudienceController extends Controller
{
    private $audience;

    public function __construct(Audience $audience, AudienceActions $audienceActions)
    {
        $this->audience = $audience;
        $this->audienceActions = $audienceActions;
    }

    public function index(Request $request)
    {
        return view('audience.index');
    }

    public function datatables()
    {
        $platformId = Auth::user()->platform_id;
        $query = $this->audience->select(
            'audiences.id',
            'audiences.name',
            'audiences.created_at',
            'audiences.condition_text',
            'audiences.description',
            'audiences.callcenter_active'
        )
            ->where('audiences.platform_id', $platformId)
            ->orderBy('audiences.created_at');
        return datatables()->eloquent($query)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data["type"] = "create";
        $data['audience'] = new Audience();
        $data['conditions'] = [new AudienceCondition()];

        $data['options'] = AudienceCondition::allAllowedOptions();

        return view('audience.edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $audience = new Audience();
        $audience->platform_id = Auth::user()->platform_id;
        $audience->name = $request->name;
        $audience->description = $request->description;
        $audience->condition_text = $this->generateTextFromConditions($request->conditions);
        $audience->save();

        $conditionModels = [];
        foreach ($request->conditions as $index => $condition) {
            $conditionModels[] = new AudienceCondition(array_merge(
                ['audience_id' => $audience->id],
                ['position' => $index],
                $condition
            ));
        }

        $audience->audienceConditions()->delete();
        $audience->audienceConditions()->saveMany($conditionModels);
    }

    private function generateTextFromConditions(array $conditions)
    {
        $text = '';
        foreach ($conditions as $condition) {
            $conditionText = ($condition['condition_type'] ?? 1) == 1 ? 'e' : 'ou';

            $fieldText = trim($condition['field_text'] ?? '');
            $operatorText = trim($condition['operator_text'] ?? '');
            $valueText = trim($condition['value_text'] ?? '');

            if ($text) {
                $text .= "\n{$conditionText} ";
            }
            $text .= "{$fieldText} {$operatorText} {$valueText}";
        }

        return $text;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Audience $audience
     * @return \Illuminate\Http\Response
     */
    public function show(Audience $audience)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Audience $audience
     * @return \Illuminate\Http\Response
     */
    public function edit(Audience $audience)
    {
        $data = [];
        $data["type"] = "edit";
        $audience = Audience::with('audienceConditions')->find($audience->id);
        $data['audience'] = $audience;
        $data['conditions'] = $audience->audienceConditions ?? [];

        $data['options'] = AudienceCondition::allAllowedOptions();

        return view('audience.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Audience $audience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Audience $audience)
    {
        $audience->name = $request->name;
        $audience->description = $request->description;
        $audience->condition_text = $this->generateTextFromConditions($request->conditions);

        $conditionModels = [];
        foreach ($request->conditions as $index => $condition) {
            $conditionModels[] = new AudienceCondition(array_merge(
                ['position' => $index],
                $condition
            ));
        }

        $audience->audienceConditions()->delete();
        $audience->audienceConditions()->saveMany($conditionModels);

        $audience->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Audience $audience
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audience $audience)
    {
        $campaigns = $audience->campaigns()->get();
        $total = count($campaigns);
        if ($total > 0) {
            return response()->json([
                'response' => 'fail',
                'message' => "Não é possível remover este público, pois existem {$total} campanhas atreladas"
            ], 409);
        }

        $audience->audienceConditions()->delete();
        $audience->delete();
        return response()->json(['response' => 'success']);
    }

    public function endAttendance(Request $request) {
        try {
            $audiences = $this->audience->select()->whereIn('id', $request->audiences)->get();
            
            $date = date('Y-m-d H:i:s');
            foreach ($audiences as $audience) {
                $audience->callcenter_active = false;
                $audience->callcenter_end_date = $date;
                $audience->save();
            }

            return response()->json([
                'data' => 'Atendimento encerrado com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function startAttendanceAgain($id) {
        try {
            $audience = $this->audience->select()->where('id', $id)->get()->first();
            
            $audience->callcenter_active = true;
            $audience->callcenter_end_date = null;
            $audience->save();

            return response()->json([
                'data' => 'Atendimento reativado com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getActions($id) {
        $audienceActions = $this->audienceActions->select()->where('audience_id', $id)->first();
        return response()->json(['data' => $audienceActions]);
    }

    public function saveActions(Request $request) {
        try {
            $audience = $this->audience->select()->where('id', $request->audience)->first();
            $audienceActions = $this->audienceActions->select()->where('audience_id', $audience->id)->first();

            if (empty($audienceActions)) {
                $audienceActions = new AudienceActions([
                    'audience_id' => $audience->id,
                    'change_card' => $request->change_card == "true" ? true : false,
                    'resend_access_data' => $request->resend_access_data == "true" ? true : false,
                    'resend_boleto' => $request->resend_boleto == "true" ? true : false,
                    'link_pending' => $request->link_pending,
                    'link_offer' => $request->link_offer
                ]);
            } else {
                $audienceActions->change_card = $request->change_card == "true" ? true : false;
                $audienceActions->resend_access_data = $request->resend_access_data == "true" ? true : false;
                $audienceActions->resend_boleto = $request->resend_boleto == "true" ? true : false;
                $audienceActions->link_pending = $request->link_pending;
                $audienceActions->link_offer = $request->link_offer;
            }

            $audienceActions->save();

            return response()->json([
                'data' => 'Ações salvas com sucesso!'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }
}
