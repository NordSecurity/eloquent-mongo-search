<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Constants\Operators;

use MyCLabs\Enum\Enum;

class LogicalOperatorName extends Enum
{
    public const AND = '$and';
    public const OR = '$or';
}
