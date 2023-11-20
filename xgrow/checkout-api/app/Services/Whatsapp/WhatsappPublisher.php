<?php

namespace App\Services\Whatsapp;

use App\Payment;
use App\Utils\Formatter;
use Illuminate\Support\Facades\Log;
use Modules\Messaging\Facade\PubSub;

class WhatsappPublisher
{
    public const CHANNEL = 'notifications_whatsapp';

    public function boletoCreated(Payment $payment): string
    {
        $subscriber = $payment->subscriber;
        $plans = $payment->plans;
        $firstPlan = $plans->first();

        $phone = Formatter::onlyDigits($subscriber->phone_country_code.$subscriber->cel_phone);

        $json = json_encode([
            //'From' => 'string',
            'To' => $phone,
            'Type' => 'Template',
            //'Text' => 'string',
            'TemplateId' => '722351539309687',
            'Fields' => [
                'property1' => $payment->order_code ?? '',
                'property2' => $plans->pluck('name')->join(', '),
                'property3' => $subscriber->name,
                'property4' => $firstPlan->product->support_email ?? '',
            ]
        ]);

        PubSub::publish(self::CHANNEL, $json);

        Log::debug('whatsapp:boleto-created:published', ['json' => $json]);

        return $json;
    }

    public function pixCreated(Payment $payment): string
    {
        $subscriber = $payment->subscriber;
        $plans = $payment->plans;
        $firstPlan = $plans->first();

        $phone = Formatter::onlyDigits($subscriber->phone_country_code.$subscriber->cel_phone);

        $json = json_encode([
            //'From' => 'string',
            'To' => $phone,
            'Type' => 'Template',
            //'Text' => 'string',
            'TemplateId' => '471535891733039',
            'Fields' => [
                'property1' => $payment->order_code ?? '',
                'property2' => $plans->pluck('name')->join(', '),
                'property3' => $subscriber->name,
                'property4' => $firstPlan->product->support_email ?? '',
                'property5' => $payment->pix_qrcode ?? '',
            ]
        ]);

        PubSub::publish(self::CHANNEL, $json);

        Log::debug('whatsapp:pix-created:published', ['json' => $json]);

        return $json;
    }

    public function paymentConfirmed(Payment $payment, string $login = '', string $password = ''): string
    {
        $subscriber = $payment->subscriber;
        $plans = $payment->plans;
        $firstPlan = $plans->first();

        $platform = $payment->platform;

        $phone = Formatter::onlyDigits($subscriber->phone_country_code.$subscriber->cel_phone);

        $json = json_encode([
            //'From' => 'string',
            'To' => $phone,
            'Type' => 'Template',
            //'Text' => 'string',
            'TemplateId' => '548941727048866',
            'Fields' => [
                'property1' => $platform->slug ?? '',
                'property2' => $subscriber->name,
                'property3' => $plans->pluck('name')->join(', '),
                'property4' => $firstPlan->product->support_email ?? '',
                'property5' => $login,
                'property6' => $password,
                'property7' => $payment->order_number,
            ]
        ]);

        PubSub::publish(self::CHANNEL, $json);

        Log::debug('whatsapp:payment-confirmed:published', [
            'product_id' => $firstPlan->product->id ?? null,
            'product_name' => $firstPlan->product->id ?? null,
            'order_number' => $payment->order_number ?? null,
        ]);

        return $json;
    }

}
