<?php

declare(strict_types=1);

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\Models\Invoices\Amount;

class Invoice extends Model
{
    protected $table = 'invoices';

    public function payers(): HasMany
    {
        return $this->hasMany(Payer::class);
    }

    public function amounts(): HasMany
    {
        return $this->hasMany(Amount::class);
    }
}
