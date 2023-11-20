<?php

namespace App\Http\Requests\Api;

use App\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListPaymentsRequest extends FormRequest
{
    public static $allowedStatusFilter = [
        Payment::STATUS_PAID,
        Payment::STATUS_PENDING,
        Payment::STATUS_CANCELED,
        Payment::STATUS_FAILED,
    ];

    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->status) {
            $this->merge([
                'status' => explode(',', $this->status),
            ]);
        }
    }

    public function rules()
    {
        return [
            'status.*' => [
                'nullable',
                Rule::in(self::$allowedStatusFilter),
            ],
        ];
    }
}
