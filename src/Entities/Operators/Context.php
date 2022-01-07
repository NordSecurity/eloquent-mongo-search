<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Entities\Operators;

class Context
{
    private $name;

    private $field;

    private $value;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
