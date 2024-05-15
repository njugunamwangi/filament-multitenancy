<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function expenses(): HasMany
    {
         return $this->hasMany(Expense::class);
    }
}
