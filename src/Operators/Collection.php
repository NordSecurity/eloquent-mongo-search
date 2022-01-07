<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Nordsec\EloquentMongoSearch\Constants\Operators\CollectionOperatorName;
use Nordsec\EloquentMongoSearch\Constants\Operators\LogicalOperatorName;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;

class Collection implements OperatorInterface
{
    public function supports(EloquentBuilder $builder, Filter $filter, Context $context): bool
    {
        if (!is_array($context->getValue()) || empty($context->getValue())) {
            return false;
        }

        $firstKey = array_keys($context->getValue())[0];

        return CollectionOperatorName::isValid($firstKey);
    }

    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        $collectionOperatorName = array_keys($context->getValue())[0];
        $isConditionInverted = $collectionOperatorName === CollectionOperatorName::NOT_IN;
        $collection = $context->getValue()[$collectionOperatorName];

        $logicalOperator = $this->mapLogicalOperator($context->getName());

        $builder->whereIn($context->getField(), $collection, $logicalOperator, $isConditionInverted);
    }

    private function mapLogicalOperator(string $logicalOperator): string
    {
        return $logicalOperator === LogicalOperatorName::AND ? 'and' : 'or';
    }
}
