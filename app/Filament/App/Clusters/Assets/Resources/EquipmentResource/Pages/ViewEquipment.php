<?php

namespace App\Filament\App\Clusters\Assets\Resources\EquipmentResource\Pages;

use App\Filament\App\Clusters\Assets\Resources\EquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEquipment extends ViewRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
