<?php

namespace App\Filament\App\Clusters\CRM\Resources\LeadResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\LeadResource;
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
