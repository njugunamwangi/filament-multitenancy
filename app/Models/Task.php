<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wallo\FilamentSelectify\Components\ToggleButton;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function company(): BelongsTo
    {
         return $this->belongsTo(Company::class);
    }

    public function expense(): HasOne
    {
         return $this->hasOne(Expense::class);
    }

    public function customer(): BelongsTo
    {
         return $this->belongsTo(Customer::class);
    }

    public function equipment(): BelongsToMany
    {
         return $this->belongsToMany(Equipment::class);
    }

    public function quote(): HasOne
    {
         return $this->hasOne(Quote::class);
    }

    public function completed()
    {
         $this->update(['is_completed' => true]);

         $this->save();
    }

    public static function getForm(): array
    {
        $company_id = Filament::getTenant()->id;

         return [
            Select::make('customer_id')
                ->relationship('customer', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('company_id', $company_id))
                ->searchable()
                ->preload()
                ->required(),
            DatePicker::make('due_date'),
            Textarea::make('description')
                ->required()
                ->columnSpanFull(),
            ToggleButton::make('requires_equipment')
                ->live(),
            ToggleButton::make('is_completed')
                ->label('Completed?'),
            Select::make('equipment')
                ->live()
                ->visible(fn(Get $get) => $get('requires_equipment'))
                ->requiredWith('requires_equipment')
                ->multiple()
                ->relationship('equipment', 'registration', modifyQueryUsing: fn (Builder $query) => $query->where('company_id', $company_id))
                ->preload()
                ->createOptionForm(Equipment::getForm())
                ->createOptionModalHeading('Create Equipment')
                ->createOptionUsing(function (array $data): int {
                    $data['company_id'] = Filament::getTenant()->id;

                    return Equipment::create($data)->getKey();
                }),
        ];
    }
}
