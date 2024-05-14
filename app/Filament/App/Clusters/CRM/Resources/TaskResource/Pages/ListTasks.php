<?php

namespace App\Filament\App\Clusters\CRM\Resources\TaskResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\TaskResource;
use App\Models\Task;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
        $company_id = Filament::getTenant()->id;

        $tabs[] = Tab::make('All')
                    ->badge(Task::where('company_id', $company_id)->count());

        $tabs[] = Tab::make('Completed')
                    ->badge(Task::where('company_id', $company_id)->where('is_completed', true)->count())
                    ->modifyQueryUsing(function($query) {
                        return $query->where('is_completed', true);
                    });

        $tabs[] = Tab::make('Completed')
                    ->badge(Task::where('company_id', $company_id)->where('is_completed', false)->count())
                    ->modifyQueryUsing(function($query) {
                        return $query->where('is_completed', false);
                    });

        return $tabs;
    }
}
