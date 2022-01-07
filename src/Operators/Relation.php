<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators;

use BadMethodCallException;
use Error;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;
use Nordsec\EloquentMongoSearch\Operators\Group\Group;

class Relation implements OperatorInterface
{
    private $childGroup;

    public function __construct(Group $childGroup)
    {
        $this->childGroup = $childGroup;
    }

    public function supports(EloquentBuilder $builder, Filter $filter, Context $context): bool
    {
        if ($filter->getAllowedRelations() === null) {
            return $this->doesRelationExist($builder, $context->getField());
        }

        return in_array($context->getField(), $filter->getAllowedRelations(), true);
    }

    protected function doesRelationExist(EloquentBuilder $builder, string $relationName): bool
    {
        try {
            $builder->getRelation($relationName);
        } catch (RelationNotFoundException|BadMethodCallException|Error $exception) {
            return false;
        }

        return true;
    }

    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        if ($context->getValue() === []) {
            return;
        }

        $this->childGroup->query($builder, $filter, $context);
    }
}
