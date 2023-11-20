<?php

namespace App\Rules\Objects;

use App\Logs\XgrowLog;
use App\Plan;
use App\Rules\Contracts\MessageStrategyInterface;
use Exception;

class DefaultActiveStrategy implements MessageStrategyInterface {

    private $subscriptions;
    private $planId;

    public function __construct(string $planId, array $subscriptions)
    {   
        $this->subscriptions = $subscriptions;
        $this->planId = $planId;
    }

    public function getMessage(): string
    {
        $message = "Verificamos que você já possui o produto ativo. Verifique na sua área de membros.";
        if (in_array($this->planId, $this->subscriptions)) {
            $planName = '';
            try {
                $planName = Plan::findOrFail($this->planId)->name;
            }
            catch(Exception $e) {
                XgrowLog::xError(
                    'Can not find the plan > ',
                    $e,
                    ['plan' => $this->planId]
                );
            }

            $message = "Verificamos que você já possui o produto {$planName} ativo. Verifique na sua área de membros.";
        }

        return $message;
    }

}