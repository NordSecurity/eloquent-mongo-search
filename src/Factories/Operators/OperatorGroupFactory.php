<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Factories\Operators;

use Nordsec\EloquentMongoSearch\Operators\Collection;
use Nordsec\EloquentMongoSearch\Operators\Comparison;
use Nordsec\EloquentMongoSearch\Operators\Group\Group;
use Nordsec\EloquentMongoSearch\Operators\Logical;
use Nordsec\EloquentMongoSearch\Operators\Relation;
use Nordsec\EloquentMongoSearch\Operators\Relations\BelongsTo;
use Nordsec\EloquentMongoSearch\Operators\Relations\BelongsToMany;
use Nordsec\EloquentMongoSearch\Operators\Relations\CatchAll;
use Nordsec\EloquentMongoSearch\Operators\Relations\HasOneOrMany;

class OperatorGroupFactory
{
    /**
     * @return Group
     */
    public function create(): Group
    {
        return new Group(
            static function (Group $globalGroup) {
                yield new Relation(
                    new Group(
                        static function () use ($globalGroup) {
                            yield new BelongsTo($globalGroup);
                            yield new BelongsToMany($globalGroup);
                            yield new HasOneOrMany($globalGroup);
                            yield new CatchAll($globalGroup);
                        }
                    )
                );
                yield new Logical($globalGroup);
                yield new Collection();
                yield new Comparison();
            }
        );
    }
}
