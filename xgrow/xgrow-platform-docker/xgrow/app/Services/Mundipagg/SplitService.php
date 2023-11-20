<?php

namespace App\Services\Mundipagg;

use App\Client;
use App\Config;
use App\Platform;
use App\Constants;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use MundiAPILib\Models\CreateSplitRequest;
use MundiAPILib\Models\CreateSplitOptionsRequest;

class SplitService extends Controller
{
    public $platform;
    public $client;
    public $split;
    public $customer_value;
    public $service_value;
    public $plans_value;
    public $price;
    public $tax_value;
    public $antecipation_value;

    public function __construct($platform_id)
    {
        $this->platform = Platform::findOrFail($platform_id);
        $this->client = Client::findOrFail($this->platform->customer_id);
    }

    public function getPaymentSplit($amountWithInterest, $amount, $installments = 1, $calcAntecipation = true)
    {

        $clientRecipientId = $this->getClientRecipient();
        $xgrowRecipientId = $this->getXgrowRecipient();
        $totalAntecipation = ($this->platform->client->is_default_antecipation_tax && $calcAntecipation) ? $this->calcAntecipation($installments ?? 1, $amountWithInterest) : 0;
        $clientTaxTransaction = $this->platform->client->tax_transaction ?? 1.5;

        $split = [new CreateSplitRequest(), new CreateSplitRequest()];

        $split[0]->recipientId = $clientRecipientId;
        $clientAmount = (round(($this->client->percent_split / 100) * $amount, 2)) - $clientTaxTransaction;
        $split[0]->amount = str_replace('.', '', (string)number_format($clientAmount + $totalAntecipation, 2, '.', '.'));
        $split[0]->type = "flat";
        $split[0]->options = new CreateSplitOptionsRequest();
        $split[0]->options->chargeRemainderFee = true;
        $split[0]->options->chargeProcessingFee = false;
        $split[0]->options->liable = true;

        $xgrowAmount = round((((100 - $this->client->percent_split) / 100) * $amount) + ($amountWithInterest - $amount), 2);
        $xgrowAmount += $clientTaxTransaction;
        if (round((($clientAmount + $xgrowAmount) - $amountWithInterest), 2) == 0.01) {
            $xgrowAmount = $xgrowAmount - 0.01;
        }

        $split[1]->recipientId = $xgrowRecipientId;
        $split[1]->amount = str_replace('.', '', (string)number_format($xgrowAmount - $totalAntecipation, 2, '.', '.'));
        $split[1]->type = "flat";
        $split[1]->options = new CreateSplitOptionsRequest();
        $split[1]->options->chargeRemainderFee = false;
        $split[1]->options->chargeProcessingFee = true;
        $split[1]->options->liable = false;

        $this->split = $split;
        $this->customer_value = $clientAmount;
        $this->service_value = $xgrowAmount;
        $this->plans_value = $amount;
        $this->price = $amountWithInterest;
        $this->tax_value = (round((((100 - $this->client->percent_split) / 100) * $amount), 2)) + $clientTaxTransaction;
        $this->antecipation_value = round($totalAntecipation, 2);

        return $split;
    }

    public function getPaymentMetadata()
    {
        return [
            'customer_value' => $this->customer_value,
            'service_value' => $this->service_value,
            'plans_value' => $this->plans_value,
            'price' => $this->price,
            'tax_value' => $this->tax_value,
            'antecipation_value' => $this->antecipation_value,
        ];
    }

    public function getClientRecipient()
    {

        if (strlen($this->platform->recipient_id) > 0) {
            return $this->platform->recipient_id;
        } else {

            $recipientService = new RecipientService($this->platform->id);

            $recipientData = new \stdClass();
            $recipientData->name = Str::limit($this->client->first_name ?? '' . ' ' . $this->client->last_name ?? '' . ' - ' . $this->platform->name ?? '', 29, '');
            $recipientData->email = Str::limit($this->client->email, 63, '');
            $recipientData->description = Str::limit($this->client->company_name ?? '' . ' - ' . $this->platform->name ?? '', 255, '');
            $recipientData->metadata = [
                'client_id' => $this->client->id,
                'platform_id' => $this->platform->id
            ];
            if ($this->client->type_person == Client::TYPE_PERSON_PHYSICAL) {
                $recipientData->document = preg_replace('/[^0-9]/', '', $this->client->cpf);
            } else {
                $recipientData->document = preg_replace('/[^0-9]/', '', $this->client->cnpj);
            }
            $recipientData->bank = $this->client->bank;
            $recipientData->branchNumber = substr(preg_replace('/[^0-9]/', '', $this->client->branch), 0, -1);
            $recipientData->branchCheckDigit = substr(preg_replace('/[^0-9]/', '', $this->client->branch), -1, 1);
            $recipientData->accountNumber = substr(preg_replace('/[^0-9]/', '', $this->client->account), 0, -1);
            $recipientData->accountCheckDigit = substr(preg_replace('/[^0-9]/', '', $this->client->account), -1, 1);
            $recipient = $recipientService->create($recipientData);

            //Store recipient
            $this->platform->recipient_id = $recipient->id;
            $this->platform->save();

            return $recipient->id;
        }

    }

    public function getXgrowRecipient()
    {

        $recipientService = new RecipientService($this->platform->id);

        $config = Config::first();

        if (strlen($config->recipient_id) > 0) {
            //Return recipient
            return $config->recipient_id;
        } else {
            $recipientData = new \stdClass();
            $recipientData->name = $config->name;
            $recipientData->email = $config->email;
            $recipientData->description = "Fandone";
            $recipientData->document = preg_replace('/[^0-9]/', '', $config->document);
            $recipientData->bank = $config->bank;
            $recipientData->branchNumber = substr(preg_replace('/[^0-9]/', '', $config->branch), 0, -1);
            $recipientData->branchCheckDigit = substr(preg_replace('/[^0-9]/', '', $config->branch), -1, 1);
            $recipientData->accountNumber = substr(preg_replace('/[^0-9]/', '', $config->account), 0, -1);
            $recipientData->accountCheckDigit = substr(preg_replace('/[^0-9]/', '', $config->account), -1, 1);
            $recipient = $recipientService->create($recipientData);

            //Store recipient
            $config->recipient_id = $recipient->id;
            $config->save();

            return $recipient->id;
        }
    }

    protected function calcAntecipation(int $installments, float $total): float
    {
        $tax = Constants::getAntecipationTax($installments);
        return round($tax * $total, 2);
    }
}
