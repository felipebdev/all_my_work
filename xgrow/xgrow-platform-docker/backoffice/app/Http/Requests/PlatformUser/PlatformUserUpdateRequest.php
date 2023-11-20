<?php

namespace App\Http\Requests\PlatformUser;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PlatformUserUpdateRequest extends FormRequest
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

        $passwordRequired = 'nullable';
        $updateFieldRequired =  'required';
        $platformFieldRequired =  'required';

        //change password
        if($this->method() == 'PATCH'){
            $passwordRequired =  'required';
            $updateFieldRequired =  'nullable';
            $platformFieldRequired =  'nullable';
        }

        return [
            'platforms' => "{$platformFieldRequired}|array",
            'platforms.*' => "{$updateFieldRequired}|exists:platforms,id",
            'name' => "{$updateFieldRequired}|min:3",
            'email' => "{$updateFieldRequired}|email|unique:platforms_users,email,{$id},id",
            'password' => [
                $passwordRequired,
                Password::min(8)
                    ->numbers()
                    ->letters()
                    ->symbols()
                    ->uncompromised()
            ],
        ];
    }
}
