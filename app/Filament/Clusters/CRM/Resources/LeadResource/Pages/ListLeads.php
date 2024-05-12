<?php

namespace App\Filament\Clusters\CRM\Resources\LeadResource\Pages;

use App\Filament\Clusters\CRM\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
