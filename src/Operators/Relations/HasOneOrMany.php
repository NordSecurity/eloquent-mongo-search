<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators\Relations;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany as EloquentHasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;

class HasOneOrMany extends BaseRelation
{
    protected function getRelationType(): string
    {
        return EloquentHasOneOrMany::class;
    }

    protected function getParentKey(Relation $relation): string
    {
        return $relation->getQualifiedParentKeyName();
    }

    protected function getSelectKey(Relation $relation): string
    {
        return $relation->getQualifiedForeignKeyName();
    }

    protected function getSelectTable(Relation $relation): string
    {
        return $relation->getQuery()->getModel()->getTable();
    }
}
