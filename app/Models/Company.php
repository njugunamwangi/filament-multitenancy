<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function logo(): BelongsTo
    {
         return $this->belongsTo(Media::class, 'logo_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function expenses(): HasMany
    {
         return $this->hasMany(Expense::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function tags(): HasMany
    {
         return $this->hasMany(Tag::class);
    }

    public function leads(): HasMany
    {
         return $this->hasMany(Lead::class);
    }

    public function equipment(): HasMany
    {
         return $this->hasMany(Equipment::class);
    }

    public function tasks(): HasMany
    {
         return $this->hasMany(Task::class);
    }

    public function quotes(): HasMany
    {
         return $this->hasMany(Quote::class);
    }
}
