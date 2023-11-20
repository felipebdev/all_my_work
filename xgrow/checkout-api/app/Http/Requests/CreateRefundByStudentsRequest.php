<?php

namespace App\Http\Requests;

use App\Services\Finances\Objects\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRefundByStudentsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_method' => [
                'required', Rule::in([Constants::XGROW_CREDIT_CARD, Constants::XGROW_BOLETO, Constants::XGROW_PIX]),
            ],
            'payment_id' => [
                'required', 'string'
            ],
            'reason' => [
                'required', 'string', 'min:10', 'max:100',
            ],
            'metadata' => [
                'nullable', 'array'
            ],

            // "boleto" requires bank data
            'bank_code' => [
                'required_if:payment_type,'.Constants::XGROW_BOLETO, 'string', 'min:3',
            ],
            'agency' => [
                'required_if:payment_type,'.Constants::XGROW_BOLETO, 'string',
            ],
            'agency_digit' => [
                'nullable', 'string',
            ],

            'account' => [
                'required_if:payment_type,'.Constants::XGROW_BOLETO, 'string',
            ],
            'account_digit' => [
                'required_if:payment_type,'.Constants::XGROW_BOLETO, 'string', 'size:1',
            ],
            'document_number' => [
                'required_if:payment_type,'.Constants::XGROW_BOLETO, 'string', 'min:11', 'max:14',
            ],
            'legal_name' => [
                'required_if:payment_type,'.Constants::XGROW_BOLETO, 'string', 'min:3',
            ],

            'refund_all' => [
                'nullable', 'boolean',
                // Affects only "sem limite". If true, refund all installments paid; if false, refund a single transaction
            ],

            'code' => 'required|numeric'
        ];
    }

}
