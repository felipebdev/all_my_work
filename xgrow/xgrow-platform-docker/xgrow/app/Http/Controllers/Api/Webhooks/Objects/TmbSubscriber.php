<?php

namespace App\Http\Controllers\Api\Webhooks\Objects;

class TmbSubscriber
{

    public string $name;
    public ?string $phone = null;
    public string $planId;
    public ?string $document = null;
    public ?string $address = null;

    public static function fromArray(array $array = []): self
    {
        $self = new self();
        $self->name = $array['cliente'] ?? $array['email'];
        $self->phone = $array['telefone_ativo'] ?? null;
        $self->document = $array['documento'] ?? null;
        $self->address = $array['endereco_completo'] ?? null;
        $self->planId = $array['id_externo'];

        return $self;
    }

    protected function __construct()
    {
    }


}
