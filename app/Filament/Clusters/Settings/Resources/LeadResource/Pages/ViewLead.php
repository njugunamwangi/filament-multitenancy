<?php

namespace App\Filament\Clusters\Settings\Resources\LeadResource\Pages;

use App\Filament\Clusters\Settings\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
