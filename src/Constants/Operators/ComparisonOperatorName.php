<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Constants\Operators;

use MyCLabs\Enum\Enum;

class ComparisonOperatorName extends Enum
{
    public const GREATER_OR_EQUAL = '$gte';
    public const GREATER = '$gt';
    public const LESS_THAN_OR_EQUAL = '$lte';
    public const LESS_THAN = '$lt';
    public const NOT_EQUAL = '$ne';

    public const VALUE_MAP = [
        ComparisonOperatorName::GREATER_OR_EQUAL => '>=',
        ComparisonOperatorName::GREATER => '>',
        ComparisonOperatorName::LESS_THAN_OR_EQUAL => '<=',
        ComparisonOperatorName::LESS_THAN => '<',
        ComparisonOperatorName::NOT_EQUAL => '!=',
    ];
}
