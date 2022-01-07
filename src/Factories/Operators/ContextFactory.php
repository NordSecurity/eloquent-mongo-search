<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Factories\Operators;

use Nordsec\EloquentMongoSearch\Entities\Operators\Context;

class ContextFactory
{
    public function create(string $field, $value, string $name = '$and'): Context
    {
        $context = new Context();
        $context->setField($field);
        $context->setValue($value);
        $context->setName($name);

        return $context;
    }
}
