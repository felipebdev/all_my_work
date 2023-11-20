<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the client is authorized to make this request.
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
        $id = Request::segment(3);

        $rules = [
            'name' => 'required|min:3',
            'analysis_status' => 'required',
            'price' => 'required',
            'type_plan' => 'required',
            'installment' => 'required',
            'checkout_address' => 'required',
            'platform_id' => 'required|exists:platforms,id'
        ];

        return $rules;
    }
}
