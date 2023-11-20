<?php

namespace App\Http\Requests\Affiliation;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AffiliationSettingsUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'enabled' => [
                'required', 'boolean',
            ],
            'approve_request_manually' => [
                'nullable', 'boolean',
            ],
            'receive_email_notifications' => [
                'nullable', 'boolean',
            ],
            'buyers_data_access_allowed' => [
                'nullable', 'boolean',
            ],
            'support_email' => [
                'nullable', 'string',
            ],
            'instructions' => [
                'nullable', 'string',
            ],
            'commission' => [
                'nullable', 'numeric',
            ],
            'cookie_duration' => [
                'nullable', 'string', Rule::in('0', '1', '30', '90', '180'),
            ],
            'assignment' => [
                'nullable', 'string', Rule::in('first_click', 'last_click'),
            ],
            'invite_link' => [
                'nullable', 'string',
            ],
        ];
    }

    /**
     * If validator fails return the exception in json form
     * @param  Validator  $validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 400));
    }
}
