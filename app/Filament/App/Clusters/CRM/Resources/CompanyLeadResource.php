<?php

namespace App\Filament\App\Clusters\CRM\Resources;

use App\Filament\App\Clusters\CRM;
use App\Filament\App\Clusters\CRM\Resources\CompanyLeadResource\Pages;
use App\Filament\App\Clusters\CRM\Resources\CompanyLeadResource\RelationManagers;
use App\Models\CompanyLead;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyLeadResource extends Resource
{
    protected static ?string $model = CompanyLead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = CRM::class;

    protected static ?string $modelLabel = 'Lead';

    protected static ?string $slug = 'leads';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('lead_id')
                    ->label('Lead')
                    ->options(Lead::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->disableOptionWhen(fn (string $value): bool => $value === 1)
                    ->in(fn (Select $component): array => array_keys($component->getEnabledOptions()))
                    ->createOptionForm(Lead::getForm())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lead.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customers')
                    ->getStateUsing(fn($record) => $record->lead->customers()->count()),
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
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\deleteAction::make()
                        ->hidden(fn($record) => $record->lead->customers()->count() > 0),
                ])
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
            'index' => Pages\ListCompanyLeads::route('/'),
            'create' => Pages\CreateCompanyLead::route('/create'),
            'view' => Pages\ViewCompanyLead::route('/{record}'),
            'edit' => Pages\EditCompanyLead::route('/{record}/edit'),
        ];
    }
}
