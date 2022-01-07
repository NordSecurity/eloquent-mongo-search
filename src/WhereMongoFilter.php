<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Factories\Operators\ContextFactory;
use Nordsec\EloquentMongoSearch\Factories\Operators\OperatorGroupFactory;

class WhereMongoFilter
{
    private $contextFactory;

    private $operatorGroupFactory;

    public function __construct(
        ContextFactory $contextFactory = null,
        OperatorGroupFactory $operatorGroupFactory = null
    ) {
        $this->contextFactory = $contextFactory ?? new ContextFactory();
        $this->operatorGroupFactory = $operatorGroupFactory ?? new OperatorGroupFactory();
    }

    public function scopeWhereFilter(
        EloquentBuilder $builder,
        Filter $filter
    ): EloquentBuilder {
        $operatorGroup = $this->operatorGroupFactory->create();
        $conditions = $filter->getConditions() ?? [];

        foreach ($conditions as $field => $value) {
            $context = $this->contextFactory->create($field, $value);
            $operatorGroup->query($builder, $filter, $context);
        }

        return $builder;
    }
}
