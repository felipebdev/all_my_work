<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinancialExportReportsRequest extends FormRequest
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
            'reportName' => 'required',
            'typeFile' => 'required'
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
            'reportName.required' => 'O nome do relatório é obrigatório e deve ser (subscription, nolimit, transactions).',
            'typeFile.required'  => 'O tipo de arquivo é obrigatório e deve ser ou xlsx ou csv'
        ];
    }
}
