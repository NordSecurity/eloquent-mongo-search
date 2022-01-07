<?php

declare(strict_types=1);

namespace Tests\Models\Invoices;

use Illuminate\Database\Eloquent\Model;

class Amount extends Model
{
    protected $table = 'invoices_amounts';
}
