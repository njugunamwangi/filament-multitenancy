<?php

namespace App\Models;

use App\Casts\Money;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'accommodation' => 'json',
            'subsistence' => 'json',
            'equipment' => 'json',
            'fuel' => 'json',
            'labor' => 'json',
            'material' => 'json',
            'misc' => 'json',
            'total' => Money::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public static function getForm(): array
    {
        return [
            Select::make('currency_id')
                ->options(Currency::all()->pluck('abbr', 'id'))
                ->default(Company::find(Filament::getTenant()->id)->currency_id)
                ->searchable()
                ->preload()
                ->optionsLimit(80)
                ->label('Currency')
                ->required(),
            Tabs::make()
                ->tabs([
                    Tabs\Tab::make('Accommodation')
                        ->icon('heroicon-o-home-modern')
                        ->schema([
                            Repeater::make('accommodation')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->schema([
                                    DatePicker::make('Date')
                                        ->required(),
                                    TextInput::make('amount')
                                        ->numeric()
                                        ->live()
                                        ->default(100),
                                ])
                                ->columns()
                                ->addActionLabel('Add Accommodation')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updatedTotals($get, $set);
                                }),
                            Placeholder::make('accommodation_totals')
                                ->label('Sub Totals')
                                ->live()
                                ->content(fn (Get $get) => $get('accommodation_totals')),
                        ]),
                    Tabs\Tab::make('Food & Beverage')
                        ->icon('heroicon-o-face-smile')
                        ->schema([
                             Repeater::make('subsistence')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->schema([
                                    DateTimePicker::make('Date')
                                        ->required(),
                                    TextInput::make('amount')
                                        ->numeric()
                                        ->live()
                                        ->default(100),
                                ])
                                ->columns()
                                ->addActionLabel('Add Subsistence')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updatedTotals($get, $set);
                                }),
                             Placeholder::make('subsistence_totals')
                                ->label('Sub Totals')
                                ->live()
                                ->content(fn (Get $get) => $get('subsistence_totals')),
                         ]),
                    Tabs\Tab::make('Equipment')
                        ->icon('heroicon-o-beaker')
                        ->schema([
                             Repeater::make('equipment')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->schema([
                                    DatePicker::make('Date')
                                        ->required(),
                                    TextInput::make('amount')
                                        ->numeric()
                                        ->live()
                                        ->default(100),
                                ])
                                ->columns()
                                ->addActionLabel('Add Equipment')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updatedTotals($get, $set);
                                }),
                             Placeholder::make('equipment_totals')
                                ->label('Sub Totals')
                                ->live()
                                ->content(fn (Get $get) => $get('equipment_totals')),
                         ]),
                    Tabs\Tab::make('Fuel & Logistics')
                        ->icon('heroicon-o-truck')
                        ->schema([
                             Repeater::make('fuel')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->schema([
                                    DatePicker::make('Date')
                                        ->required(),
                                    TextInput::make('amount')
                                        ->numeric()
                                        ->live()
                                        ->default(100),
                                ])
                                ->columns()
                                ->addActionLabel('Add Item')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updatedTotals($get, $set);
                                }),
                             Placeholder::make('fuel_totals')
                                ->label('Sub Totals')
                                ->live()
                                ->content(fn (Get $get) => $get('fuel_totals')),
                         ]),
                    Tabs\Tab::make('Labor')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->schema([
                             Repeater::make('labor')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->schema([
                                    DatePicker::make('Date')
                                        ->required(),
                                    TextInput::make('amount')
                                        ->numeric()
                                        ->live()
                                        ->default(100),
                                ])
                                ->columns()
                                ->addActionLabel('Add Labor')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updatedTotals($get, $set);
                                }),
                             Placeholder::make('labor_totals')
                                ->label('Sub Totals')
                                ->live()
                                ->content(fn (Get $get) => $get('labor_totals')),
                         ]),
                    Tabs\Tab::make('Material')
                        ->icon('heroicon-o-adjustments-vertical')
                        ->schema([
                             Repeater::make('material')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->schema([
                                    DatePicker::make('Date')
                                        ->required(),
                                    Textarea::make('description'),
                                    TextInput::make('amount')
                                        ->numeric()
                                        ->required()
                                        ->default(100),
                                ])
                                ->columns(3)
                                ->addActionLabel('Add Material')
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updatedTotals($get, $set);
                                }),
                             Placeholder::make('material_totals')
                                ->label('Sub Totals')
                                ->live()
                                ->content(fn (Get $get) => $get('material_totals')),
                         ]),
                    Tabs\Tab::make('Miscellaneous')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->schema([
                             Repeater::make('misc')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->schema([
                                    TextInput::make('quantity')
                                        ->numeric()
                                        ->default(1)
                                        ->required(),
                                    Textarea::make('Description')
                                        ->required(),
                                    TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->numeric()
                                        ->default(100)
                                        ->required(),
                                ])
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::updatedTotals($get, $set);
                                })
                                ->columns(3)
                                ->addActionLabel('Add Misc.'),
                             Placeholder::make('misc_totals')
                                ->label('Sub Totals')
                                ->live()
                                ->content(fn (Get $get) => $get('misc_totals')),
                         ]),
                ]),
            TextInput::make('total')
                ->readOnly()
                ->label('Total Expenses')
                // ->hidden()
                ->live()
                ->afterStateHydrated(function (Get $get, Set $set) {
                    self::updatedTotals($get, $set);
                }),
        ];
    }

    public static function updatedTotals(Get $get, Set $set): void
    {
        // accommodation
        $accommodation = collect($get('accommodation'));

        $accommodationTotals = 0;

        foreach ($accommodation as $item) {
            $accommodationTotals += $item['amount'];
        }

        // equipment
        $subsistence = collect($get('subsistence'));

        $subsistenceTotals = 0;

        foreach ($subsistence as $item) {
            $subsistenceTotals += $item['amount'];
        }

        // equipment
        $equipment = collect($get('equipment'));

        $equipmentTotals = 0;

        foreach ($equipment as $item) {
            $equipmentTotals += $item['amount'];
        }

        // fuel
        $fuel = collect($get('fuel'));

        $fuelTotals = 0;

        foreach ($fuel as $item) {
            $fuelTotals += $item['amount'];
        }

        // labor
        $labor = collect($get('labor'));

        $laborTotals = 0;

        foreach ($labor as $item) {
            $laborTotals += $item['amount'];
        }

        // material
        $material = collect($get('material'));

        $materialTotals = 0;

        foreach ($material as $item) {
            $materialTotals += $item['amount'];
        }

        // miscellaneous
        $miscellaneous = collect($get('misc'));

        $miscTotals = 0;

        foreach ($miscellaneous as $misc) {
            $aggregate = $misc['quantity'] * $misc['unit_price'];

            $miscTotals += $aggregate;
        }

        $set('accommodation_totals', number_format($accommodationTotals));
        $set('subsistence_totals', number_format($subsistenceTotals));
        $set('equipment_totals', number_format($equipmentTotals));
        $set('fuel_totals', number_format($fuelTotals));
        $set('labor_totals', number_format($laborTotals));
        $set('material_totals', number_format($materialTotals));
        $set('misc_totals', number_format($miscTotals));

        $totals = $accommodationTotals + $subsistenceTotals + $equipmentTotals + $fuelTotals + $laborTotals + $materialTotals + $miscTotals;
        $set('total', $totals);

    }
}
