<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Settings::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('abbr')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('locale')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('precision')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('subunit_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subunit')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('symbol')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('symbol_first')
                    ->required(),
                Forms\Components\TextInput::make('decimal_mark')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('thousands_separator')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('abbr')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('abbr')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('locale')
                    ->searchable(),
                Tables\Columns\TextColumn::make('precision')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subunit_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subunit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->searchable(),
                Tables\Columns\IconColumn::make('symbol_first')
                    ->boolean(),
                Tables\Columns\TextColumn::make('decimal_mark')
                    ->searchable(),
                Tables\Columns\TextColumn::make('thousands_separator')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'view' => Pages\ViewCurrency::route('/{record}'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
