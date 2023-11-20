<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
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
        $user = auth('api')->user();
        $passwordRequired = ($this->method() == 'PUT') ? 'nullable' : 'required';

        $rules = [
            'name' => 'required|max:191',
            'email' => "required|email|unique:users,email," . $user->id,
            'password' => [$passwordRequired, Password::min(5)->numbers()->letters()->symbols()],
            'two_factor_enabled' => 'sometimes',
        ];

        return $rules;
    }
}
