<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthorPost extends FormRequest
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
        $type_file_image = config('constants.type_file.image');

        return [
            'name_author' => "required|min:3|max:191",
            'image' => "sometimes|required|mimes:{$type_file_image}|dimensions:max_width=720,max_height=720"
        ];
    }
}
