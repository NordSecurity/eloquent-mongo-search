<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Checkers;

class TypeChecker
{
    public function isScalarOrNull($value): bool
    {
        return is_scalar($value) || $value === null;
    }

    public function isListItem($field, $value): bool
    {
        return is_numeric($field) && is_array($value);
    }
}
