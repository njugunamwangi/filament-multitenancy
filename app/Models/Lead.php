<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function companies(): BelongsToMany
    {
         return $this->belongsToMany(Company::class);
    }
}
