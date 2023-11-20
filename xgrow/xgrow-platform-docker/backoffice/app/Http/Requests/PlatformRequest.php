<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PlatformRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge(
            ['restrict_ips' => $this->restrict_ips === 'true']
        );
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
            'name' => "required|unique:platforms,name,{$id},id",
            'customer_id' => "required|numeric|exists:clients,id",

            'url' => "nullable|unique:platforms,url,{$id},id",
            'slug' => "nullable|max:50|unique:platforms,slug,{$id},id",
            'restrict_ips' => "nullable",
            'ips_available' => "nullable:restrict_ips,==,true",
            'name_slug' => "nullable",
            'cover' => "nullable|image",
        ];
    }

    public function attributes()
    {
        if (App::isLocale('pt-BR')) {
            return [
                'name' => "Nome da plataforma",
                'url' => "URL da plataforma",
                'slug' => "Endereço recomendado",
                'restrict_ips' => "Lista de IP's",
                'ips_available' => "IP's",
                'customer_id' => "Id do cliente",
                'name_slug' => "Slug do endereço recomendado",
                'cover' => "Imagem da plataforma",
            ];
        }

        return [
            'name' => "platform name",
            'url' => "platform URL",
            'slug' => "recommended address",
            'restrict_ips' => "IP's list",
            'ips_available' => "IP's",
            'customer_id' => "customer id",
            'name_slug' => "recommended address slug",
            'cover' => "platform image",
        ];
    }
}
