<?php

namespace App\Models;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public static function getForm(): array
    {
         return [
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('website_url')
                ->required()
                ->unique(ignoreRecord: true),
         ];
    }

    public function equipment(): HasMany
    {
         return $this->hasMany(Equipment::class);
    }
}
