<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductLinksRequest extends FormRequest
{
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
            'product_id' => 'required',
            'plan_id' => 'required',
            'link_name' => 'required',
            'url' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_id' => 'O Id do produto é obrigatório',
            'plan_id' => 'O Id do plano é obrigatório',
            'link_name' => 'O nome do link é obrigatório',
            'url' => 'A URL é obrigatória',
            'script_code' => 'A geração do script é obrigatória',
        ];
    }
}
