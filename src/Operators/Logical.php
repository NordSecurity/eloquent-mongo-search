<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Nordsec\EloquentMongoSearch\Constants\Operators\LogicalOperatorName;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;
use Nordsec\EloquentMongoSearch\Factories\Operators\ContextFactory;
use Nordsec\EloquentMongoSearch\Operators\Group\Group;

class Logical implements OperatorInterface
{
    private $childGroup;

    private $contextFactory;

    public function __construct(Group $childGroup)
    {
        $this->childGroup = $childGroup;

        $this->contextFactory = new ContextFactory();
    }

    public function supports(EloquentBuilder $builder, Filter $filter, Context $context): bool
    {
        return LogicalOperatorName::isValid($context->getField());
    }

    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        $builder->where(
            function (EloquentBuilder $builder) use ($filter, $context) {
                $this->queryWithLogicalStatements($builder, $filter, $context);
            }
        );
    }

    private function queryWithLogicalStatements(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        foreach ($context->getValue() as $field => $conditions) {
            if (is_string($field)) {
                $this->queryWithScalar($builder, $filter, $field, $conditions, $context);
                continue;
            }

            $this->queryWithArray($builder, $filter, $conditions, $context);
        }
    }

    private function queryWithScalar(
        EloquentBuilder $builder,
        Filter $filter,
        string $field,
        array $conditions,
        Context $context
    ): void {
        $childContext = $this->contextFactory->create($field, $conditions, $context->getField());
        if (!$this->childGroup->supports($builder, $filter, $childContext)) {
            return;
        }

        $this->childGroup->query($builder, $filter, $childContext);
    }

    private function queryWithArray(EloquentBuilder $builder, Filter $filter, array $conditions, Context $context): void
    {
        foreach ($conditions as $field => $value) {
            $childContext = $this->contextFactory->create($field, $value, $context->getField());
            $this->childGroup->query($builder, $filter, $childContext);
        }
    }
}
