<?php

namespace App\Http\Controllers;

use App\CallcenterConfig;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallCenterConfigController extends Controller
{
    public function __construct(CallcenterConfig $config)
    {
        $this->config = $config;
    }

    public function index()
    {
        // GET THE CALLCENTER CONFIG
        $config = $this->config
                        ->where('platform_id', '=', Auth::user()->platform_id)
                        ->get()
                        ->first();
        
        // CREATE CONFIG IF DOESN'T EXIST
        if (empty($config)) {
            $config = $this->config->create([
                'active' => false,
                'period_restriction' => false,
                'ip_restriction' => false,
                'allow_changes' => false,
                'limit_leads' => false,
                'allow_reasons_loss' => false,
                'allow_reasons_gain' => false,
                'show_email' => false,
                'show_address' => false,
                'platform_id' => Auth::user()->platform_id
            ]);
        }

        $restrictedIp = new CallCenterRestrictedIpController();
        $reasonsLoss = new CallCenterReasonsLossController();
        $reasonsGain = new CallCenterReasonsGainController();

        $data['config'] = $config;
        $data['restrictedIp'] = $restrictedIp->index($config->id);
        $data['reasonsLoss'] = $reasonsLoss->index($config->id);
        $data['reasonsGain'] = $reasonsGain->index($config->id);

        return view('callcenter.config.index', $data);
    }

    public function update(Request $request, $id)
    {
        $config = $this->config::findOrFail($id);

        // CALL CENTER STATUS
        $config->active = $request->active ?? false;

        // PERIOD RESTRICION
        $config->period_restriction = $request->period_restriction ?? false;
        if ($request->period_restriction == true) {
            $test = $this->verifyDates($request->initial_date, $request->initial_hour, $request->final_date, $request->final_hour);
            if (!$test[0])
                return back()->withInput()->withErrors($test[1]);

            $config->initial_date = DateTime::createFromFormat("d/m/Y", $request->initial_date)->format('Y-m-d');
            $config->initial_hour = $request->initial_hour;
            $config->final_date = DateTime::createFromFormat("d/m/Y", $request->final_date)->format('Y-m-d');
            $config->final_hour = $request->final_hour;
        }

        // ALLOW CHANGE EMAIL AND PASSOWORD
        $config->allow_changes = $request->allow_changes ?? false;

        // LIMIT THE NUMBER OF LEADS
        $config->limit_leads = $request->limit_leads ?? false;
        if ($request->limit_leads == true) {
            if (empty($request->number_leads)) {
                return back()->withInput()->withErrors(['number_leads' => 'Escolha um número de leads']);
            }

            $config->number_leads = $request->number_leads;
        }
        
        // SHOW THE EMAIL ADDRESS TO THE ATTENDANT
        $config->show_email = $request->show_email ?? false;

        // SHOW THE ADDRESS TO THE ATTENDANT
        $config->show_address = $request->show_address ?? false;
        
        // RESTRICTED IP ADDRESS
        $config->ip_restriction = $request->ip_restriction ?? false;
        $restrictedIp = new CallCenterRestrictedIpController();
        if (!empty($request->ip_to_delete)) {
            $restrictedIp->delete(explode('|', $request->ip_to_delete), $id);
        }
        if (!empty($request->ip)) {
            $restrictedIp->update($request->ip, $id);
        }
        if (!empty($request->newip)) {
            $restrictedIp->store($request->newip, $id);
        }

        // REASONS OF LOSS
        $config->allow_reasons_loss = $request->allow_reasons_loss ?? false;
        $reasonsLoss = new CallCenterReasonsLossController();
        if (!empty($request->reason_loss_to_delete)) {
            $reasonsLoss->delete(explode('|', $request->reason_loss_to_delete), $id);
        }
        if (!empty($request->reasonLoss)) {
            $reasonsLoss->update($request->reasonLoss, $id);
        }
        if (!empty($request->newreasonloss)) {
            $reasonsLoss->store($request->newreasonloss, $id);
        }


        // REASONS OF GAIN
        $config->allow_reasons_gain = $request->allow_reasons_gain ?? false;
        $reasonsGain = new CallCenterReasonsGainController();
        if (!empty($request->reason_gain_to_delete)) {
            $reasonsGain->delete(explode('|', $request->reason_gain_to_delete), $id);
        }
        if (!empty($request->reasonGain)) {
            $reasonsGain->update($request->reasonGain, $id);
        }
        if (!empty($request->newreasongain)) {
            $reasonsGain->store($request->newreasongain, $id);
        }



        // SAVE ALL INFORMARTION
        if (!$config->save()) {
            return back()->withInput()->withErrors(['error' => 'Ocorreu um erro inesperado, tente novamente mais tarde']);
        }

        return back()->with('success', 'Dados atualizados com sucesso!');
    }

    public function verifyDates($initial_date, $initial_hour, $final_date, $final_hour)
    {
        if (
            empty($initial_date) ||
            empty($initial_hour) ||
            empty($final_date) ||
            empty($final_hour)
        ) {
            return [false, ['period_restriction' => 'Preencha todos as informações do período']];
        }

        $format_idate = DateTime::createFromFormat("d/m/Y H:i", $initial_date.' '.$initial_hour)->format('Y-m-d H:i:s');
        $format_fdate = DateTime::createFromFormat("d/m/Y H:i", $final_date.' '.$final_hour)->format('Y-m-d H:i:s');

        if (strtotime($format_fdate) <= strtotime($format_idate)) {
            return [false, ['period_restriction' => 'A data e a hora final precisam ser maiores que a data e hora inicial']];
        }

        return [true];
    }
}
