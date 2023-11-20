<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class StoreUpdateRequest extends FormRequest
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
        $id = Request::segment(3);

        $passwordRequired = ($this->method() == 'PUT') ? 'nullable' : 'required';

        $rules = [
            'name' => 'required|min:3',
            'email' => "required|email|unique:users,email,{$id},id",
            'active' => 'required|boolean',
            'two_factor_enabled' => 'required|boolean',
            'type_access' => 'required|in:full,restrict',
            'back_permission_id' => 'required_if:type_access,==,restrict|exists:back_permissions,id',
            'password' => [
                $passwordRequired,
                'confirmed',
                Password::min(8)
                    ->numbers()
                    ->letters()
                    ->symbols()
            ],
        ];

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'password' => 'senha',
            'type_access' => 'tipo de permissões',
            'back_permission_id' => 'grupo de permissões',
        ];
    }
}
