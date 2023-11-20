<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransferRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //'recipient_id' => 'required|string|min:1',
            'amount' => 'required|int|min:100|max:2147483647', // max: 2^15-1
            'message' => 'nullable|string',
            'metadata' => 'nullable|array',
        ];
    }
}
