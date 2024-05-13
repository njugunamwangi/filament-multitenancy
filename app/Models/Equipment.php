<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function company(): BelongsTo
    {
         return $this->belongsTo(Company::class);
    }

    public function brand(): BelongsTo
    {
         return $this->belongsTo(Brand::class);
    }

    public function equipment(): BelongsToMany
    {
         return $this->belongsToMany(Equipment::class);
    }

    public static function getForm(): array
    {
         return [
            Select::make('brand_id')
                ->relationship('brand', 'name')
                ->createOptionForm(Brand::getForm())
                ->required(),
            TextInput::make('registration')
                ->required()
                ->maxLength(255),
         ];
    }
}
