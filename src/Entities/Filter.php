<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Entities;

class Filter
{
    private $conditions;

    private $allowedColumns;

    private $allowedRelations;

    public function getConditions(): ?array
    {
        return $this->conditions;
    }

    public function setConditions(?array $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function getAllowedColumns(): ?array
    {
        return $this->allowedColumns;
    }

    public function setAllowedColumns(?array $allowedColumns): self
    {
        $this->allowedColumns = $allowedColumns;

        return $this;
    }

    public function getAllowedRelations(): ?array
    {
        return $this->allowedRelations;
    }

    public function setAllowedRelations(?array $allowedRelations): self
    {
        $this->allowedRelations = $allowedRelations;

        return $this;
    }
}
