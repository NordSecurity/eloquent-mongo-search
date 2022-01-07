<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Constants\Operators;

use MyCLabs\Enum\Enum;

class CollectionOperatorName extends Enum
{
    public const IN = '$in';
    public const NOT_IN = '$nin';
}
