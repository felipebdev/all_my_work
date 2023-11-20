<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use SoftDeletes, HasFactory;

    protected $hidden = ['password'];

    const STATUS_ACTIVE = 'active';
    const STATUS_TRIAL = 'trial';
    const STATUS_CANCELED = 'canceled';
    const STATUS_LEAD = 'lead';
    const STATUS_PENDING_PAYMENT = 'pending_payment';

    const TYPE_NATURAL_PERSON = 'natural_person';
    const TYPE_LEGAL_PERSON = 'legal_person';

    const DOCUMENT_TYPE_CPF = 'CPF';
    const DOCUMENT_TYPE_CNPJ = 'CNPJ';

    static function allStatus()
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_TRIAL => 'Trial',
            self::STATUS_CANCELED => 'Cancelado',
            self:: STATUS_LEAD => 'Lead',
            self::STATUS_PENDING_PAYMENT => 'Pagamento Pendente',
        ];
    }
}
