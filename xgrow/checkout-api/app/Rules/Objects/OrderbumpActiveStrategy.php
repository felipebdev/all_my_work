<?php

namespace App\Rules\Objects;

use App\Logs\XgrowLog;
use App\Plan;
use App\Rules\Contracts\MessageStrategyInterface;
use Exception;

class OrderbumpActiveStrategy implements MessageStrategyInterface {

    private $subscriptions;
    private $planId;
    private $orderbump;

    public function __construct(string $planId, array $subscriptions, array $orderbump)
    {   
        $this->subscriptions = $subscriptions;
        $this->planId = $planId;
        $this->orderbump = $orderbump;
    }

    public function getMessage(): string
    {
        $defaultPlanName = $this->getPlanName($this->planId);
        $orderbumpPlanName = $this->getPlanName(reset($this->orderbump));
        $message = "Verificamos que você já possui o produto ativo. Verifique na sua área de membros.";

        if (in_array($this->planId, $this->subscriptions) && //default active
            count(array_intersect($this->orderbump, $this->subscriptions)) === 0 //orderbump not active
        ) {
            $message = "Verificamos que você já possui o produto {$defaultPlanName} ativo. Para adquirir o produto {$orderbumpPlanName}, entre em contato com o suporte.";
        }
        else if (!in_array($this->planId, $this->subscriptions) && //default not active
            count(array_intersect($this->orderbump, $this->subscriptions)) !== 0 //orderbump active
        ) {
            $message = "Verificamos que você já possui o produto {$orderbumpPlanName} ativo. Remova-o do carrinho e compre somente o produto principal.";
        }
        else if (in_array($this->planId, $this->subscriptions) && //default active
            count(array_intersect($this->orderbump, $this->subscriptions)) !== 0 //orderbump active
        ) {
            $message = "Verificamos que você já possui os produtos {$defaultPlanName} e {$orderbumpPlanName} ativos. Verifique na sua área de membros.";
        }

        return $message;
    }

    private function getPlanName(string $planId): string 
    {
        if (empty($planId)) return '';
        
        $planName = '';
        try {
            $planName = Plan::findOrFail($planId)->name;
        }
        catch(Exception $e) {
            XgrowLog::xError(
                'Can not find the plan > ',
                $e,
                ['plan' => $planId]
            );
        }

        return $planName;
    }
}