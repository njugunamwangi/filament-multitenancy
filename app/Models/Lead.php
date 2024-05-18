<?php

namespace App\Models;

use App\Models\Company\Lead as CompanyLead;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function companyLeads(): HasMany
    {
         return $this->hasMany(CompanyLead::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required(),
        ];
    }
}
