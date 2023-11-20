<?php

namespace App\Http\Requests\PlatformUser;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PlatformUserStoreRequest extends FormRequest
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
            'platforms' => 'required|array',
            'platforms.*' => "exists:platforms,id",
            'name' => 'required|min:3',
            'accessPermition' => ['required', Rule::in(['full', 'restrict'])],
            'email' => "required|email|unique:platforms_users",
            'password' => [
                "required",
                Password::min(8)
                    ->numbers()
                    ->letters()
                    ->symbols()
            ],
        ];
    }
}
