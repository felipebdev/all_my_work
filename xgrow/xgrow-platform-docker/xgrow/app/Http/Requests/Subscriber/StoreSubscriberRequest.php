<?php

namespace App\Http\Requests\Subscriber;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'full_name' => 'required|string|min:3',
            'email' => 'required|email:strict,dns',
            'plan_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'full_name' => 'Nome completo',
            'email' => 'Email',
            'plan_id' => 'Produto',
        ];
    }
}
