<?php

namespace App\Repositories\Campaign;

use App\AudienceCondition;
use Carbon\Carbon;
use Exception;

class AudienceConditionSqlGenerator
{
    private $validOptions;

    public function __construct()
    {
        $this->validOptions = collect(AudienceCondition::allAllowedOptions());
    }

    public function getSqlWhereClauseFromConditions(iterable $conditions)
    {
        $sql = '';
        $params = [];
        foreach ($conditions as $condition) {
            $sql = $this->getSqlWhereClause(
                $condition->field,
                $condition->operator,
                $condition->value,
                $condition->condition_type ?? 1,
                $params,
                $sql
            );
        }

        return [$sql, $params];
    }

    protected function getSqlWhereClause(
        string $field,
        int $operator,
        string $value,
        int $conditionType,
        array &$params,
        string $previousCondition = ''
    ) {
        if (!$this->isFieldAllowed($field)) {
            throw new Exception('Invalid field');
        }

        $adjustedField = $this->adjustField($field, $value);

        $logicalOperator = $this->getLogicalOperator($conditionType);

        $comparisonOperator = $this->getComparisonOperator($operator);

        $params[] = $this->castValue($operator, $field, $value);

        $whereClause = "{$adjustedField} {$comparisonOperator} ?"; // "FIELD COMPARISON_OPERATOR ?"
        if (!$previousCondition) {
            return $whereClause;
        }

        // ( (...) AND {FIELD COMPARISON_OPERATOR XXXXX} )
        return "( {$previousCondition} {$logicalOperator} ({$whereClause}) )";
    }

    private function isFieldAllowed(string $field)
    {
        $fields = $this->validOptions->map(function ($option) {
            return $option->value;
        });

        if ($fields->contains($field)) {
            return true;
        }

        return false;
    }

    private function getLogicalOperator(int $conditionType)
    {
        $conditions = [
            AudienceCondition::CONDITION_TYPE['and'] => 'AND',
            AudienceCondition::CONDITION_TYPE['or'] => 'OR'
        ];

        $newCondition = $conditions[$conditionType] ?? null;
        if (is_null($newCondition)) {
            throw new Exception('Invalid logical operator');
        }

        return $newCondition;
    }

    private function getComparisonOperator(int $operator): string
    {
        $operators = [
            AudienceCondition::OPERATOR['eq'] => '=',
            AudienceCondition::OPERATOR['ne'] => '<>',
            AudienceCondition::OPERATOR['gt'] => '>',
            AudienceCondition::OPERATOR['gte'] => '>=',
            AudienceCondition::OPERATOR['lt'] => '<',
            AudienceCondition::OPERATOR['lte'] => '<=',
            AudienceCondition::OPERATOR['isNull'] => '<=>',
        ];

        $newOperator = $operators[$operator] ?? null;
        if (is_null($newOperator)) {
            throw new Exception('Invalid comparison operator');
        }

        return $newOperator;
    }

    private function castValueByType(string $value, int $valueType) // : mixed
    {
        $newValue = null;
        switch ($valueType) {
            case AudienceCondition::VALUE_TYPE['number']:
                $newValue = (float)$value;
                break;
            case AudienceCondition::VALUE_TYPE['int']:
                $newValue = (int)$value;
                break;
            case AudienceCondition::VALUE_TYPE['string']:
                $newValue = "{$value}";
                break;
            case AudienceCondition::VALUE_TYPE['date']:
                $newValue = Carbon::createFromFormat('d/m/Y', $value)->toDateString();
                break;
            case AudienceCondition::VALUE_TYPE['datetime']:
                $newValue = Carbon::createFromFormat('d/m/Y', $value)->toDateString();
                break;
        }

        if (is_null($newValue)) {
            throw new Exception('Invalid value type');
        }

        return $newValue;
    }

    private function adjustField(string $field, string $value) // : mixed
    {
        $option = $this->validOptions->where('value', $field)->first();

        if ($option->value_type == AudienceCondition::VALUE_TYPE['datetime']) {
            return "DATE({$field})";
        } elseif ($field == 'subscriber_status_lead') {
            return "subscribers.status";
        } elseif ($field == 'payment_singlesale_status') {
            return "payments.type = 'P' AND payments.status";
        } elseif ($field == 'payment_subscription_status') {
            if ($value == 'active' || $value == 'inactive') {
                return "payments.type = 'R' AND subscribers.status";
            }
            return "payments.type = 'R' AND payments.status";
        } elseif ($field == 'payment_nolimit_status') {
            return "payments.type = 'U' AND payments.status";
        }

        return "{$field}";
    }

    private function castValue(string $operator, string $field, string $value) // : mixed
    {
        if ($operator == AudienceCondition::OPERATOR['isNull']) {
            return null;
        }

        $option = $this->validOptions->where('value', $field)->first();

        $valueType = $option->value_type;

        return $this->castValueByType($value, $valueType);
    }

}
