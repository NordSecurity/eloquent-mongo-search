<?php

declare(strict_types=1);

namespace Nordsec\EloquentMongoSearch\Operators\Group;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Nordsec\EloquentMongoSearch\Entities\Filter;
use Nordsec\EloquentMongoSearch\Entities\Operators\Context;
use Nordsec\EloquentMongoSearch\Factories\Operators\ContextFactory;
use Nordsec\EloquentMongoSearch\Operators\OperatorInterface;

class Group implements OperatorInterface
{
    protected $operators;

    protected $contextFactory;

    public function __construct(Closure $operators)
    {
        $this->operators = $operators;
        $this->contextFactory = new ContextFactory();
    }

    public function supports(EloquentBuilder $builder, Filter $filter, Context $context): bool
    {
        $operatorsGenerator = ($this->operators)($this);

        /** @var OperatorInterface $operator */
        foreach ($operatorsGenerator as $operator) {
            if (!$operator->supports($builder, $filter, $context)) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function query(EloquentBuilder $builder, Filter $filter, Context $context): void
    {
        $operatorsGenerator = ($this->operators)($this);

        /** @var OperatorInterface $operator */
        foreach ($operatorsGenerator as $operator) {
            if (!$operator->supports($builder, $filter, $context)) {
                continue;
            }

            $operator->query($builder, $filter, $context);
            break;
        }
    }
}
