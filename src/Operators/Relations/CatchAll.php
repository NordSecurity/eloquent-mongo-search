<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;
use Nordsec\EloquentMongoSearch\Factories\Operators\ContextFactory;
use Nordsec\EloquentMongoSearch\Operators\Group\Group;
use Nordsec\EloquentMongoSearch\Operators\OperatorInterface;

class CatchAll implements OperatorInterface
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
        return true;
    }

    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        $relation = $builder->getRelation($context->getField());

        $builder
            ->whereHas(
                $relation,
                function (EloquentBuilder $builder) use ($context, $filter) {
                    foreach ($context->getValue() as $relationField => $relationValue) {
                        $childContext = $this->contextFactory->create(
                            $relationField,
                            $relationValue,
                            $context->getName()
                        );

                        $this->childGroup->query($builder, $filter, $childContext);
                    }
                }
            );
    }
}
