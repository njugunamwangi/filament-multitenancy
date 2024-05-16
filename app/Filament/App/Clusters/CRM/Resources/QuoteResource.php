<?php

namespace App\Filament\App\Clusters\CRM\Resources;

use App\Filament\App\Clusters\CRM;
use App\Filament\App\Clusters\CRM\Resources\QuoteResource\Pages;
use App\Filament\App\Clusters\CRM\Resources\QuoteResource\RelationManagers;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Quote;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Wallo\FilamentSelectify\Components\ToggleButton;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = CRM::class;

    public static function form(Form $form): Form
    {
        $company_id = Filament::getTenant()->id;

        return $form
            ->schema([
                Section::make('User Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('customer_id')
                                    ->relationship('customer', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('company_id', $company_id))
                                    ->required()
                                    ->live(),
                                Forms\Components\Select::make('task_id')
                                    ->visible(fn(Get $get) => $get('customer_id'))
                                    ->live()
                                    ->relationship('task', 'id' , modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('company_id', $company_id)->where('customer_id', $get('customer_id'))->whereDoesntHave('quote')),
                            ]),
                            Forms\Components\Select::make('currency_id')
                                ->relationship('currency', 'abbr')
                                ->default(Company::find($company_id)->currency_id)
                                ->searchable()
                                ->preload()
                                ->optionsLimit(100)
                                ->live()
                                ->required(),
                            ToggleButton::make('mail')
                                ->required(),
                    ]),
                Group::make()
                    ->columnSpanFull()
                    ->schema([
                        Group::make()
                            ->schema([
                                Repeater::make('items')
                                    ->schema([
                                        Textarea::make('description')
                                            ->required(),
                                        TextInput::make('quantity')
                                            ->default(1)
                                            ->required()
                                            ->numeric(),
                                        TextInput::make('unit_price')
                                            ->default(10000)
                                            ->required()
                                            ->live()
                                            ->numeric(),
                                    ])
                                    ->addActionLabel('Add Item')
                                    ->columns(3)
                                    ->live()
                                    ->afterStateUpdated(function(Get $get, Set $set) {
                                        self::updatedTotals($get, $set);
                                    })
                                    ->deleteAction(
                                        fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updatedTotals($get, $set)),
                                    )
                            ])->columnSpan(8),
                        Group::make()
                            ->schema([
                                TextInput::make('subtotal')
                                    ->readOnly()
                                    ->prefix(fn(Get $get) => Currency::find($get('currency_id'))->abbr ?? 'CUR')
                                    ->afterStateHydrated(function(Get $get, Set $set) {
                                        self::updatedTotals($get, $set);
                                    }),
                                TextInput::make('taxes')
                                    ->suffix('%')
                                    ->numeric()
                                    ->default(20)
                                    ->afterStateUpdated(function(Get $get, Set $set) {
                                        self::updatedTotals($get, $set);
                                    }),
                                TextInput::make('total')
                                    ->prefix(fn(Get $get) => Currency::find($get('currency_id'))->abbr ?? 'CUR')
                                    ->readOnly(),
                            ])->columnSpan(4)
                    ])
                    ->columns(12),
                RichEditor::make('notes')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function updatedTotals(Get $get, Set $set): void
    {
        $items = collect($get('items'));

        $subtotal = 0;

        foreach($items as $item) {
            $aggregate = $item['quantity'] * $item['unit_price'];

            $subtotal += $aggregate;
        }

        $set('subtotal', number_format($subtotal));
        $set('total', number_format($subtotal + ($subtotal * ($get('taxes') / 100))));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task.id')
                    ->getStateUsing(fn($record) => '#'.$record->task->id)
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.abbr')
                    ->description(fn($record) => $record->currency->name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('taxes')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('serial')
                    ->searchable(),
                Tables\Columns\IconColumn::make('mail')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
