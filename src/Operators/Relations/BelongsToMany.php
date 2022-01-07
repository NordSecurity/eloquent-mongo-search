<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;

class BelongsToMany extends BaseRelation
{
    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        $relation = $builder->getRelation($context->getField());

        if ($this->relationSameAsFromClause($builder, $relation)) {
            return;
        }

        parent::query($builder, $filter, $context);
    }

    private function relationSameAsFromClause(EloquentBuilder $builder, EloquentRelation $relation): bool
    {
        return $builder->getQuery()->from === $relation->getRelated()->newQuery()->getQuery()->from;
    }

    protected function getRelationType(): string
    {
        return EloquentBelongsToMany::class;
    }

    protected function getParentKey(EloquentRelation $relation): string
    {
        return $relation->getQualifiedParentKeyName();
    }

    protected function getSelectKey(EloquentRelation $relation): string
    {
        return $relation->getQualifiedForeignPivotKeyName();
    }

    protected function getSelectTable(EloquentRelation $relation): string
    {
        return $relation->getTable();
    }
}
