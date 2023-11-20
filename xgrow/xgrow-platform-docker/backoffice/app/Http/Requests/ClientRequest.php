<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ClientRequest extends FormRequest
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

        $passwordRequired = ($this->method() == 'PUT') ? 'nullable' : 'required';

        $rules = [
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'image' => 'nullable|mimes:png,jpg,jpeg',
            'email' => "required|email|unique:clients,email,{$id},id",
            'password' => [$passwordRequired, 'confirmed', Password::min(5)->numbers()->letters()->symbols()],
            'verified' => 'sometimes',
            'type_person' => ['required', Rule::in(['J', 'F'])],
            'cpf' => 'required_if:type_person,==,F',
            'cnpj' => 'required_if:type_person,==,J',
            "fantasy_name" => "required_if:type_person,==,J|max:191",
            "company_name" => "required_if:type_person,==,J|max:191",
            "company_url" => "required_if:type_person,==,J|max:191",
            "address" => "nullable|max:60",
            "number" => "nullable|max:10",
            "complement" => "nullable|max:20",
            "district" => "nullable|max:30",
            "city" => "nullable|max:40",
            "state" => "nullable|max:2",
            "zipcode" => "nullable|max:20",
            "percent_split" => 'required|numeric',
            "tax_transaction" => 'required|numeric',
            "recipient_id" => "nullable|max:191",
            // Bank data
            "bank" => "required|max:10",
            "branch" => "required|max:6",
            "account" => "required|max:15",
            "account_type" => "required",
            "holder_name" => "required",
            "branch_check_digit" => "nullable",
            "account_check_digit" => "required",
            "statement_descriptor" => "nullable|max:22",
        ];

        return $rules;
    }
}
