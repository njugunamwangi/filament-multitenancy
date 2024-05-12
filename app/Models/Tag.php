<?php

namespace App\Models;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function company(): BelongsTo
    {
         return $this->belongsTo(Company::class);
    }

    public function customers(): BelongsToMany
    {
         return $this->belongsToMany(Customer::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ColorPicker::make('color')
                ->required(),
        ];
    }
}
