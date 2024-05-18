<?php

namespace App\Livewire;

use App\Filament\App\Clusters\CRM\Resources\TaskResource;
use App\Models\Task;
use Filament\Facades\Filament;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class TaskCalender extends FullCalendarWidget
{
    public function fetchEvents(array $fetchInfo): array
    {
        return Task::query()
            ->where('due_date', '>=', $fetchInfo['start'])
            ->where('due_date', '<=', $fetchInfo['end'])
            ->where('company_id', Filament::getTenant()->id)
            ->get()
            ->map(
                fn (Task $task) => EventData::make()
                    ->id($task->id)
                    ->title(strip_tags($task->description))
                    ->start($task->due_date)
                    ->end($task->due_date)
                    ->url(TaskResource::getUrl('view', [$task->id]))
                    ->toArray()
            )
            ->toArray();
    }
}
