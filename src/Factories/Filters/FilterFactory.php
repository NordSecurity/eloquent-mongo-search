<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Factories\Filters;

use Nordsec\EloquentMongoSearch\Entities\Filter;

class FilterFactory
{
    public function create(
        array $conditions,
        ?array $allowedColumns = [],
        ?array $allowedRelations = []
    ): Filter {
        $filter = new Filter();
        $filter->setAllowedColumns($allowedColumns);
        $filter->setAllowedRelations($allowedRelations);
        $filter->setConditions($conditions);

        return $filter;
    }
}
