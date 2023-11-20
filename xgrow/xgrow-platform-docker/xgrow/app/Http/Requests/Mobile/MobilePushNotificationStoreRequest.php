<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

class MobilePushNotificationStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string',
            'text' => 'required|string',
            'run_at' => 'required|date',
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
