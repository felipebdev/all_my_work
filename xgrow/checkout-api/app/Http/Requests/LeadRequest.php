<?php

namespace App\Http\Requests;

use App\Lead;
use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'platform_id' => 'required|exists:platforms,id',
            'subscriber_id' => 'required|exists:subscribers,id',
            'type' => 'required|in:'. Lead::TYPE_PRODUCT .','. Lead::TYPE_ORDER_BUMP .','. Lead::TYPE_UPSELL,
            'plan_id' => 'required|exists:plans,id',
        ];
    }
}
