<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use stdClass;

class AudienceCondition extends Model
{

    public const OPERATOR = [
        'eq' => 1,
        'ne' => 2,
        'gt' => 3,
        'gte' => 4,
        'lt' => 5,
        'lte' => 6,
        'isNull' => 7,
    ];

    public const VALUE_TYPE = [
        'number' => 1,
        'int' => 2,
        'string' => 3,
        'date' => 4,
        'datetime' => 5,
    ];

    public const CONDITION_TYPE = [
        'and' => 1,
        'or' => 2,
    ];


    protected $fillable = [
        'id',
        'audience_id',
        'position',
        'field',
        'operator',
        'value',
        'value_type',
        'condition_type'
    ];

    public static function allAllowedOptions()
    {
        return [
            self::createOption('products.id', self::VALUE_TYPE['int'], 'Produto'),
            self::createOption('subscribers.status', self::VALUE_TYPE['string'], 'Status'),
            self::createOption('subscriber_status_lead', self::VALUE_TYPE['string'], 'Tipo de aluno'),
            self::createOption('subscribers.created_at', self::VALUE_TYPE['datetime'], 'Data de cadastro'),
            self::createOption('subscribers.last_acess', self::VALUE_TYPE['datetime'], 'Último acesso'),
            self::createOption('subscribers.gender', self::VALUE_TYPE['string'], 'Gênero'),
            self::createOption('subscribers.birthday', self::VALUE_TYPE['date'], 'Data de nascimento'),
            self::createOption('subscribers.address_state', self::VALUE_TYPE['string'], 'Estado'),
            self::createOption('subscribers.address_city', self::VALUE_TYPE['string'], 'Cidade'),
            self::createOption('subscribers.document_type', self::VALUE_TYPE['string'], 'Pessoa'),
            self::createOption('payments.type_payment', self::VALUE_TYPE['string'], 'Método de pagamento'),
            self::createOption('payment_singlesale_status', self::VALUE_TYPE['string'], 'Venda única'),
            self::createOption('payment_subscription_status', self::VALUE_TYPE['string'], 'Assinatura'),
            self::createOption('payment_nolimit_status', self::VALUE_TYPE['string'], 'Sem limite'),
        ];
    }

    private static function createOption(string $value, string $valueType, string $text)
    {
        $obj = new stdClass();
        $obj->value = $value;
        $obj->value_type = $valueType;
        $obj->text = $text;
        return $obj;
    }

    public function audience()
    {
        return $this->belongsTo(Audience::class);
    }
}
