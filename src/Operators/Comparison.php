<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Nordsec\EloquentMongoSearch\Checkers\TypeChecker;
use Nordsec\EloquentMongoSearch\Constants\Operators\ComparisonOperatorName;
use Nordsec\EloquentMongoSearch\Constants\Operators\LogicalOperatorName;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;

class Comparison implements OperatorInterface
{
    private const DEFAULT_MAPPED_COMPARISON_OPERATOR = '=';

    private $typeChecker;

    public function __construct()
    {
        $this->typeChecker = new TypeChecker();
    }

    public function supports(EloquentBuilder $builder, Filter $filter, Context $context): bool
    {
        if ($this->areAllColumnsAllowed($filter->getAllowedColumns())) {
            return true;
        }

        return in_array($context->getField(), $filter->getAllowedColumns(), true);
    }

    private function areAllColumnsAllowed(?array $allowedColumns): bool
    {
        return $allowedColumns === null;
    }

    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        if ($this->typeChecker->isScalarOrNull($context->getValue())) {
            $this->queryWithScalar(
                $builder,
                $context,
                self::DEFAULT_MAPPED_COMPARISON_OPERATOR,
                $context->getField(),
                $context->getValue()
            );

            return;
        }

        foreach ($context->getValue() as $field => $value) {
            if ($this->typeChecker->isScalarOrNull($value)) {
                $this->queryWithScalar($builder, $context, $field, $context->getField(), $value);
                continue;
            }

            $this->queryWithArray($builder, $context, $value);
        }
    }

    private function queryWithArray(EloquentBuilder $builder, Context $context, array $array): void
    {
        foreach ($array as $field => $value) {
            $this->queryWithScalar($builder, $context, $field, $context->getField(), $value);
        }
    }

    private function queryWithScalar(
        EloquentBuilder $builder,
        Context $context,
        string $comparisonOperatorName,
        $field,
        $value
    ): void {
        $comparisonOperator = $this->mapComparisonOperator($comparisonOperatorName);
        if ($context->getName() === LogicalOperatorName::OR) {
            $builder->orWhere($context->getField(), $comparisonOperator, $value);
            return;
        }

        $builder->where($field, $comparisonOperator, $value);
    }

    private function mapComparisonOperator(string $comparisonOperatorName): string
    {
        return ComparisonOperatorName::VALUE_MAP[$comparisonOperatorName] ?? self::DEFAULT_MAPPED_COMPARISON_OPERATOR;
    }
}
