<?php

namespace App\Filament\App\Clusters\CRM\Resources\TaskResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\TaskResource;
use App\Models\Expense;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            ActionGroup::make([
                Actions\EditAction::make(),
                Action::make('expenses')
                    ->label('Track Expenses')
                    ->modalDescription('Expenses for task #'.$record->id)
                    ->stickyModalFooter()
                    ->stickyModalHeader()
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('danger')
                    ->modalWidth(MaxWidth::SevenExtraLarge)
                    ->modalSubmitActionLabel('Save')
                    ->fillForm(fn ($record): array => [
                        'currency_id' => $record->expense?->currency_id,
                        'accommodation' => $record->expense?->accommodation,
                        'subsistence' => $record->expense?->subsistence,
                        'equipment' => $record->expense?->equipment,
                        'fuel' => $record->expense?->fuel,
                        'labor' => $record->expense?->labor,
                        'material' => $record->expense?->material,
                        'misc' => $record->expense?->misc,
                    ])
                    ->form(Expense::getForm())
                    ->action(function($record, array $data) {
                        if ($record->expense) {
                            $record->expense()->update([
                                'company_id' => Filament::getTenant()->id,
                                'currency_id' => $data['currency_id'],
                                'accommodation' => $data['accommodation'],
                                'subsistence' => $data['subsistence'],
                                'equipment' => $data['equipment'],
                                'fuel' => $data['fuel'],
                                'labor' => $data['labor'],
                                'material' => $data['material'],
                                'misc' => $data['misc'],
                                'total' => $data['total'],
                            ]);
                        } else {
                            $record->expense()->create([
                                'company_id' => Filament::getTenant()->id,
                                'currency_id' => $data['currency_id'],
                                'accommodation' => $data['accommodation'],
                                'subsistence' => $data['subsistence'],
                                'equipment' => $data['equipment'],
                                'fuel' => $data['fuel'],
                                'labor' => $data['labor'],
                                'material' => $data['material'],
                                'misc' => $data['misc'],
                                'total' => $data['total'],
                            ]);
                        }
                    })
                    ->after(function ($record) {
                        if ($record->expense) {
                            Notification::make()
                                ->title('Expense updated')
                                ->info()
                                ->icon('heroicon-o-check')
                                ->body('Task expenses have been updated successfully')
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Expense created')
                                ->success()
                                ->icon('heroicon-o-check')
                                ->body('Task expenses have been created successfully')
                                ->send();
                        }
                    })
            ])
        ];
    }
}
