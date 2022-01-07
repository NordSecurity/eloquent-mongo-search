<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

class BelongsTo extends BaseRelation
{
    protected function getRelationType(): string
    {
        return EloquentBelongsTo::class;
    }

    protected function getParentKey(Relation $relation): string
    {
        return $relation->getQualifiedForeignKeyName();
    }

    protected function getSelectKey(Relation $relation): string
    {
        return $relation->getQuery()->getModel()->getKeyName();
    }

    protected function getSelectTable(Relation $relation): string
    {
        return  $relation->getQuery()->getModel()->getTable();
    }
}
