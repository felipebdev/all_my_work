<?php

namespace App\Http\Requests\Subscriber;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberRefundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string|in:boleto,credit_card,pix',
            'payment_plan_id' => 'required',
            'reason' => 'required|string|between:10,50',
        ];
    }

    public function attributes()
    {
        return [
            'type' => 'Tipo de reembolso',
            'payment_plan_id' => 'Identificação do pagamento',
            'reason' => 'Motivo do estorno',
        ];
    }
}
