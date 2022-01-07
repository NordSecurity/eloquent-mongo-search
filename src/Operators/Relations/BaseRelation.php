<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Nordsec\EloquentMongoSearch\Checkers\TypeChecker;
use Nordsec\EloquentMongoSearch\Constants\Operators\LogicalOperatorName;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;
use Nordsec\EloquentMongoSearch\Factories\Operators\ContextFactory;
use Nordsec\EloquentMongoSearch\Operators\Group\Group;
use Nordsec\EloquentMongoSearch\Operators\OperatorInterface;

abstract class BaseRelation implements OperatorInterface
{
    private $childGroup;

    private $contextFactory;

    private $typeChecker;

    public function __construct(
        Group $childGroup
    ) {
        $this->childGroup = $childGroup;

        $this->contextFactory = new ContextFactory();
        $this->typeChecker = new TypeChecker();
    }

    public function supports(EloquentBuilder $builder, Filter $filter, Context $context): bool
    {
        $relation = $builder->getRelation($context->getField());

        return is_a($relation, $this->getRelationType());
    }

    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        $relation = $builder->getRelation($context->getField());

        $parentKey = $this->getParentKey($relation);
        $selectKey = $this->getSelectKey($relation);
        $selectTable = $this->getSelectTable($relation);

        if ($context->getName() === LogicalOperatorName::OR) {
            $whereInMethod = [$builder, 'orWhereIn'];
        } else {
            $whereInMethod = [$builder, 'whereIn'];
        }

        $whereInMethod(
            $parentKey,
            function (QueryBuilder $queryBuilder) use ($relation, $filter, $selectKey, $selectTable, $context) {

                $builder = new EloquentBuilder($queryBuilder);
                $builder->setModel($relation->newModelInstance());

                $builder
                    ->select($selectKey)
                    ->from($selectTable);

                foreach ($context->getValue() as $field => $value) {
                    if ($this->typeChecker->isListItem($field, $value)) {
                        $this->queryWithArray($builder, $filter, $context, $value);
                        continue;
                    }

                    $this->queryWithScalar($builder, $filter, $context, $field, $value);
                }
            }
        );
    }

    private function queryWithArray(EloquentBuilder $builder, Filter $filter, Context $context, array $conditions): void
    {
        foreach ($conditions as $field => $value) {
            $this->queryWithScalar($builder, $filter, $context, $field, $value);
        }
    }

    protected function queryWithScalar(EloquentBuilder $builder, Filter $filter, Context $context, $field, $value): void
    {
        $childContext = $this->contextFactory->create(
            $field,
            $value,
            $context->getField()
        );

        $this->childGroup->query($builder, $filter, $childContext);
    }

    abstract protected function getRelationType(): string;

    abstract protected function getParentKey(Relation $relation): string;

    abstract protected function getSelectKey(Relation $relation): string;

    abstract protected function getSelectTable(Relation $relation): string;
}
