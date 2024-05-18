<?php

namespace App\Filament\App\Clusters\CRM\Resources;

use App\Filament\App\Clusters\CRM;
use App\Filament\App\Clusters\CRM\Resources\LeadResource\Pages;
use App\Filament\App\Clusters\CRM\Resources\LeadResource\RelationManagers;
use App\Models\Company\Lead;
use App\Models\Lead as ModelsLead;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $cluster = CRM::class;
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('company_id', Filament::getTenant()->id)->count();
    }
    public static function form(Form $form): Form
    {
        $company = Filament::getTenant();

        return $form
            ->schema([
                Forms\Components\Select::make('lead_id')
                    ->required()
                    ->label('Lead')
                    ->options(function() use ($company) {
                        $selected = Lead::where('company_id', $company->id)->pluck('lead_id')->toArray();

                        return ModelsLead::whereNotIn('id', $selected)->pluck('name', 'id');
                    })
                    ->createOptionForm(ModelsLead::getForm())
                    ->createOptionModalHeading('Create Lead')
                    ->createOptionUsing(fn (array $data): int => ModelsLead::create($data)->getKey()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lead.name')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\EditAction::make()
                ->slideOver(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListLeads::route('/'),
            // 'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            // 'edit' => Pages\EditLead::route('/{record}/edit'),
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
