<?php

namespace App\Http\Requests;

use App\Services\Finances\Transfer\Objects\TransferFilter;
use Illuminate\Foundation\Http\FormRequest;

class ListTransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //'recipient_id' => 'required|string|min:1',
            'count' => 'nullable|int',
            'page' => 'nullable|int',
            'bank_account_id' => 'nullable|string',
            'amount' => 'nullable|int',
            'transfer_id' => 'nullable|string',
            'created_after' => 'nullable|date_format:'.TransferFilter::$dateFormat,
            'created_before' => 'nullable|date_format:'.TransferFilter::$dateFormat,
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->created_after) {
            $this->merge(['created_after' => str_replace("T", " ", $this->created_after)]);
        }

        if ($this->created_before) {
            $this->merge(['created_before' => str_replace("T", " ", $this->created_before)]);
        }
    }
}
