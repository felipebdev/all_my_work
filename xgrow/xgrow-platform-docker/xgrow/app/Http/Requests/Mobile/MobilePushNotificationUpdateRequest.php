<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

class MobilePushNotificationUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // "sometimes" means optional field (but blocks "field : null")
        return [
            'title' => 'sometimes|string|min:2',
            'text' => 'sometimes|string|min:4',
            'run_at' => 'sometimes|date',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'título',
            'text' => 'texto',
            'run_at' => 'data/hora',
        ];
    }
}
