<?php

namespace App\Filament\Clusters\Assets\Resources\BrandResource\Pages;

use App\Filament\Clusters\Assets\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBrand extends ViewRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
