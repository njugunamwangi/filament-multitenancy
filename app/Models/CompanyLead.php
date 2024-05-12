<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyLead extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company(): BelongsTo
    {
         return $this->belongsTo(Company::class);
    }

    public function lead(): BelongsTo
    {
         return $this->belongsTo(Lead::class);
    }

    public function customers(): HasMany
    {
         return $this->hasMany(Customer::class);
    }
}
