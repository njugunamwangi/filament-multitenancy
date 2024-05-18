<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Clusters\CRM\Resources\CustomerResource;
use App\Filament\App\Clusters\CRM\Resources\TaskResource;
use App\Models\Task;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class IncompleteTasksTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $company = Filament::getTenant();

        return $table
            ->query(Task::query()->where('company_id', $company->id)->where('is_completed', false))
            ->defaultSort('due_date', 'asc')
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->url(fn ($record) => CustomerResource::getUrl('view', ['record' => $record->customer_id]))
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View Task')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Task $record): string => TaskResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
