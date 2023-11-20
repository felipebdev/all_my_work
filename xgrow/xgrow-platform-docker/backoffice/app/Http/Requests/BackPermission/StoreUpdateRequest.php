<?php

namespace App\Http\Requests\BackPermission;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'name' => 'required|max:191',
            'description' => 'nullable|max:191',
            'permissions' => 'required',
            'permissions.*.back_role_id' => [
                'required',
                'distinct',
                'exists:back_roles,id'
            ],
            'permissions.*.type_access' => [
                'required',
                'in:full,restrict'
            ],
            'permissions.*.actions' => [
                'required_if:permissions.*.type_access,==,restrict',
                'exists:back_actions,id'
            ]
        ];
    }
}
