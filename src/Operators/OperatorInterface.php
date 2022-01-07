<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;

interface OperatorInterface
{
    public function supports(EloquentBuilder $builder, Filter $filter, Context $context): bool;

    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void;
}
