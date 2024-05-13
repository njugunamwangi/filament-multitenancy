<?php

namespace App\Filament\App\Clusters\Assets\Resources\EquipmentResource\Pages;

use App\Filament\App\Clusters\Assets\Resources\EquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
