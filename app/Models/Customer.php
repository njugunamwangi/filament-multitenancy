<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tags(): BelongsToMany
    {
         return $this->belongsToMany(Tag::class);
    }

    public function lead(): BelongsTo
    {
         return $this->belongsTo(Lead::class);
    }

    public function documents(): HasMany
    {
         return $this->hasMany(Document::class);
    }

    public function tasks(): HasMany
    {
         return $this->hasMany(Task::class);
    }

    public function completeTasks(): HasMany
    {
        return $this->hasMany(Task::class)
                        ->where('is_completed', true);
    }

    public function incompleteTasks(): HasMany
    {
        return $this->hasMany(Task::class)
                        ->where('is_completed', false);
    }

    public function quotes(): HasMany
    {
         return $this->hasMany(Quote::class);
    }

    public function invoices(): HasMany
    {
         return $this->hasMany(Invoice::class);
    }
}
