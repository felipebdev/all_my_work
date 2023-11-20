<?php

namespace App\Http\Controllers;

use App\ChargeRuler;
use App\Email;
use App\Services\ChargeRulerSettings;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class RulerController extends Controller
{
    public function index(Request $request)
    {
        $platformId = Auth::user()->platform_id;

        $boletos = ChargeRuler::where('platform_id', $platformId)->where('type', ChargeRuler::TYPE_BOLETO)->get();
        if ($boletos->count() == 0) {
            $boletos = ChargeRulerSettings::defaultNotificationsForBoleto($platformId, $isActive = false);
        }

        $subscriptions = ChargeRuler::where('platform_id', $platformId)->where('type', ChargeRuler::TYPE_SUBSCRIPTION)->get();
        if ($subscriptions->count() == 0) {
            $subscriptions = ChargeRulerSettings::defaultChargesForSubscription($platformId, $isActive = false);
        }

        $nolimits = ChargeRuler::where('platform_id', $platformId)->where('type', ChargeRuler::TYPE_NOLIMIT)->get();
        if ($nolimits->count()  == 0) {
            $nolimits = ChargeRulerSettings::defaultChargesForNolimit($platformId, $isActive = false);
        }

        $accesses = ChargeRuler::where('platform_id', $platformId)->where('type', ChargeRuler::TYPE_ACCESS)->get();
        if ($accesses->count() == 0) {
            $accesses = ChargeRulerSettings::defaultNotificationsForAccess($platformId, $isActive = false);
        }

        $emails = Email::all();

        return view('ruler.ruler')
            ->with('boletos', $boletos)
            ->with('subscriptions', $subscriptions)
            ->with('nolimits', $nolimits)
            ->with('accesses', $accesses)
            ->with('emails', $emails);
    }

    public function save(Request $request)
    {
        $platformId = Auth::user()->platform_id;
        $lines = $request->lines ?? [];

        $checkboxes = $request->checkbox ?? [];
        $intervals = $request->interval ?? [];
        $emailIds = $request->email_id ?? [];

        $type = $this->getTypeFromRequest($request);

        $hash = $this->getHash($request->type);

        try {
            $values = $this->prepareLines($platformId, $type, $lines, $checkboxes, $intervals, $emailIds);
        } catch (Exception $e) {
            $messages = explode(';', $e->getMessage());
            return redirect()->to(url()->previous() . $hash)->withErrors($messages);
        }

        DB::beginTransaction();
        try {
            ChargeRuler::where('platform_id', $platformId)->where('type', $type)->delete();
            ChargeRuler::insert($values);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();
            throw $e;
        }

        return redirect()->to(url()->previous() . $hash)->withSuccess('Salvo com sucesso');
    }

    private function getTypeFromRequest($request): ?string
    {
        $typeMapping = [
            'boleto' => ChargeRuler::TYPE_BOLETO,
            'nolimit' => ChargeRuler::TYPE_NOLIMIT,
            'subscription' => ChargeRuler::TYPE_SUBSCRIPTION,
            'access' => ChargeRuler::TYPE_ACCESS,
        ];

        return $typeMapping[$request->type] ?? null;
    }

    /**
     * Used by front-end to set active tab on redirect
     *
     * @param  string  $type
     * @return string
     */
    private function getHash(string $type)
    {
        $hashByType = [
            'boleto' => '#nav-boleto-tab',
            'nolimit' => '#nav-nolimit-tab',
            'subscription' => '#nav-subscription-tab',
            'access' => '#nav-access-tab',
        ];

        return $hashByType[$type] ?? '';
    }

    private function prepareLines(
        string $platformId,
        string $type,
        array $lines,
        array $checkboxes,
        array $intervals,
        array $emailIds
    ) {
        $hasCancelOption = false;
        $errors = [];
        $values = [];
        foreach ($lines as $position) {
            $isActive = isset($checkboxes[$position]);
            $interval = $intervals[$position] ?? null;
            $emailId = $emailIds[$position] ?? null;

            if ($isActive) {
                if (!$interval) {
                    $errors[] = "Intervalo obrigatório para {$position}ª Mensagem";
                }

                if (!$emailId) {
                    $errors[] = "E-mail obrigatório para {$position}ª Mensagem";
                } else {
                    $hasCancelOption |= ChargeRulerSettings::isCancelRequired($emailId);
                }
            }

            $values[] = [
                'platform_id' => $platformId,
                'type' => $type,
                'position' => $position,
                'active' => $isActive,
                'interval' => $interval,
                'email_id' => $emailId,
            ];
        }

        if (!$hasCancelOption && in_array($type, [ChargeRuler::TYPE_SUBSCRIPTION, ChargeRuler::TYPE_NOLIMIT])) {
            $errors[] = "Deve haver pelo menos um email de cancelamento";
        }

        if (count($errors)) {
            throw new Exception(join('; ', $errors));
        }

        return $values;
    }






}
