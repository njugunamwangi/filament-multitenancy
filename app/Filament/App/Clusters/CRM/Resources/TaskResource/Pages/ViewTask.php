<?php

namespace App\Filament\App\Clusters\CRM\Resources\TaskResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
