<?php

namespace Modules\Integration\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Integration\Enums\TypeEnum;
use RuntimeException;

class StoreIntegrationRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        $integration = $this->factory($this->type);
        return $integration->getRules($this->request);
    }

    public function attributes() {
        $integration = $this->factory($this->type);
        return $integration->getAttributes();
    }

    protected function prepareForValidation() {
        if (!$this->request->has('is_active')) {
            $this->merge(['is_active' => false]);
        }

        $integration = $this->factory($this->type);
        $appendData = $integration->appendData();
        if (!empty($appendData)) $this->merge($appendData);
    }

    private function factory(string $type) {
        $namespace = '\Modules\Integration\Requests\Rules';
        $integration = ucfirst(TypeEnum::getValue(strtoupper($type)));
        if (empty($integration)) throw new RuntimeException('Integration type not found');
        $validationClass = "{$namespace}\\Store{$integration}Rule";

        return new $validationClass();
    }
}
