<?php

namespace App\Http\Requests;

use App\Services\Finances\Objects\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRefundRequest extends FormRequest
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
                'string', 'prohibits:payment_plan_ids'
            ],
            'payment_plan_id' => [
                'string', 'prohibits:payment_id'
            ],
            'reason' => [
                'required', 'string', 'min:10', 'max:100',
            ],
            'metadata' => [
                'nullable', 'array'
            ],

            // "boleto" requires bank data
            'bank_code' => [
                'required_if:payment_method,'.Constants::XGROW_BOLETO, 'string', 'min:3',
            ],
            'agency' => [
                'required_if:payment_method,'.Constants::XGROW_BOLETO, 'string',
            ],
            'agency_digit' => [
                'nullable', 'string',
            ],

            'account' => [
                'required_if:payment_method,'.Constants::XGROW_BOLETO, 'string',
            ],
            'account_digit' => [
                'required_if:payment_method,'.Constants::XGROW_BOLETO, 'string', 'size:1',
            ],
            'account_type' => [
                'required_if:payment_method,'.Constants::XGROW_BOLETO, 'string', Rule::in([
                    Constants::XGROW_ACCOUNT_TYPE_CHECKING,
                    Constants::XGROW_ACCOUNT_TYPE_SAVINGS
                ]),
            ],
            'document_number' => [
                'required_if:payment_method,'.Constants::XGROW_BOLETO, 'string', 'min:11', 'max:14',
            ],
            'legal_name' => [
                'required_if:payment_method,'.Constants::XGROW_BOLETO, 'string', 'min:3',
            ],

            'refund_all' => [
                'nullable', 'boolean',
                // Affects only "sem limite". If true, refund all installments paid; if false, refund a single transaction
            ]
        ];
    }

}
