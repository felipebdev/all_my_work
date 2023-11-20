<?php

namespace App\Http\Requests\Integrations;

use App\Constants;
use Illuminate\Foundation\Http\FormRequest;

class StoreIntegrationRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        $integration = $this->factory($this->id_integration);
        return $integration->getRules();
    }

    public function attributes() {
        $integration = $this->factory($this->id_integration);
        return $integration->getAttributes();
    }

    private function factory(string $idIntegration) {
        $namespace = '\App\Http\Requests\Integrations\Rules';
        $integration = ucfirst(strtolower(Constants::getIntegrationNameById(intval($idIntegration))));
        $validationClass = "{$namespace}\\{$integration}ValidationRule";
        return new $validationClass();
    }
}