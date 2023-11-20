<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{   
    const URL_REGEX = "/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/";

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'currency' => 'required',
            'price' => 'required',
            'type_plan' => 'required',
            'checkout_layout' => 'required',
            'email' => 'nullable|email',
            'message_success_checkout' => 'required_without:url_checkout_confirm',
            'url_checkout_confirm' => ['nullable', 'regex:'.self::URL_REGEX],
            'checkout_url_terms' => ['nullable', 'regex:'.self::URL_REGEX],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes() {
        return [
            'code' => 'código',
            'description' => 'descrição',
            'maturity' => 'validade',
            'plans' => 'produto',
            'usage_limit' => 'nº limite de uso',
            'value' => 'desconto',
            'value_type' => 'tipo de desconto'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() {
        $this->merge([
            'maturity' => implode('-', array_reverse(explode('/', $this->maturity))),
            'value' => str_replace(',', '.',str_replace('.', '', $this->value)),
        ]);
    }
}
