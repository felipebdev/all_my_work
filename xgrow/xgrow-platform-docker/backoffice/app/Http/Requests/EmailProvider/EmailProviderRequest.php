<?php

namespace App\Http\Requests\EmailProvider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class EmailProviderRequest extends FormRequest
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

        return [
            'name' => "required|regex:/^[a-z_]+$/|unique:email_providers,name,{$id},id",
            'from_name' => "required",
            'from_address' => "required|email",
            'driver' => 'required',
            'settings' => 'required|json'
        ];
    }
}
