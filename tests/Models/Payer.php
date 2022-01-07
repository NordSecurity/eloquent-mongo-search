<?php

declare(strict_types=1);

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\Models\Payers\Metadata;

class Payer extends Model
{
    protected $table = 'payers';

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function metadata(): HasMany
    {
        return $this->hasMany(Metadata::class);
    }
}
