<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function company(): BelongsTo
    {
         return $this->belongsTo(Company::class);
    }

    public function currency(): BelongsTo
    {
         return $this->belongsTo(Currency::class);
    }
}
