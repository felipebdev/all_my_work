<?php

namespace App\Http\Requests;

use App\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
        if ($this->value_type == 'V' && $this->plans) {
            $plans = Plan::where('platform_id', Auth::user()->platform_id)->whereIn('id', $this->plans)->get('price');
            $cheaper = $plans->min('price');

            $minCheckout = 5;
            $maxDiscount = (int)$cheaper - $minCheckout;
            if ($maxDiscount < 0) {
                $maxDiscount = 0;
            }

            $valueCondition = "|min:0.01|max:{$maxDiscount}";
        } elseif ($this->value_type == 'P') {
            $valueCondition = '|min:1|max:90';
        } else {
            $valueCondition = '';
        }

        return [
            'code' => 'required|min:7|max:20',
            'description' => 'required|max:120',
            'value_type' => 'required|in:V,P',
            'value' => 'required|numeric' . $valueCondition,
            'usage_limit' => 'nullable|numeric',
            'maturity' => 'nullable|date',
            'plans' => 'required|array'
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
            'code' => strtoupper($this->code),
        ]);
    }
}
