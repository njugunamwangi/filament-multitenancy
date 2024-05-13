<?php

namespace App\Filament\App\Clusters\CRM\Resources;

use App\Filament\App\Clusters\CRM;
use App\Filament\App\Clusters\CRM\Resources\TaskResource\Pages;
use App\Filament\App\Clusters\CRM\Resources\TaskResource\RelationManagers;
use App\Models\Equipment;
use App\Models\Task;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Wallo\FilamentSelectify\Components\ToggleButton;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = CRM::class;

    public static function form(Form $form): Form
    {
        $company_id = Filament::getTenant()->id;

        return $form
            ->schema(Task::getForm());
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Task Information')
                    ->headerActions([
                        Action::make('completed')
                            ->visible(fn($record) => !$record->is_completed)
                            ->label('Mark as completed')
                            ->requiresConfirmation()
                            ->action(function($record) {
                                $record->completed();

                                Notification::make()
                                    ->title('Task Completed')
                                    ->body('You marked task #' .$record->id. ' as completed')
                                    ->success()
                                    ->send();
                            })
                    ])
                    ->schema([
                        TextEntry::make('customer.name'),
                        TextEntry::make('due_date')
                            ->date(),
                        IconEntry::make('is_completed')
                            ->boolean()
                            ->label('Completed'),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Section::make('Equipment')
                    ->visible(fn($record) => $record->equipment)
                    ->schema([
                        RepeatableEntry::make('equipment')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('registration'),
                                TextEntry::make('brand.name'),
                            ])
                            ->columns(2)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            // ...
            Pages\ViewTask::class,
            Pages\EditTask::class,
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
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
