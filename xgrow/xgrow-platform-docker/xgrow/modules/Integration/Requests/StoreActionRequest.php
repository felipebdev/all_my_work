<?php

namespace Modules\Integration\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Integration\Enums\ActionEnum;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Enums\TypeEnum;
use RuntimeException;

class StoreActionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $globalRules = [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'plans' => 'required|array',
            'products' => 'sometimes|array',
            'event' => ['required', Rule::in(EventEnum::getAllValues())],
            'action' => ['required', Rule::in(ActionEnum::getAllValues())],
        ];

        $integration = $this->factory($this->type);
        return array_merge($globalRules, $integration->getRules($this->request));
    }

    public function attributes()
    {
        $globalAttributes = [
            'description' => 'nome da ação',
            'is_active' => 'ativo',
            'plans' => 'plano(s)',
            'products' => 'produto(s)',
            'event' => 'quando houver (evento)',
            'action' => 'faça (ação)',
        ];

        $integration = $this->factory($this->type);
        return array_merge($globalAttributes, $integration->getAttributes());
    }

    protected function prepareForValidation()
    {
        if (!$this->request->has('is_active')) {
            $this->merge([
                'is_active' => false
            ]);
        }

        $integration = $this->factory($this->type);
        $appendData = $integration->appendData();
        if (!empty($appendData)) $this->merge($appendData);
    }

    private function factory(string $type)
    {
        $namespace = '\Modules\Integration\Requests\Rules';
        $integration = ucfirst(TypeEnum::getValue(strtoupper($type)));
        if (empty($integration)) throw new RuntimeException('Integration type not found');
        $validationClass = "{$namespace}\\Action{$integration}Rule";

        return new $validationClass();
    }
}
