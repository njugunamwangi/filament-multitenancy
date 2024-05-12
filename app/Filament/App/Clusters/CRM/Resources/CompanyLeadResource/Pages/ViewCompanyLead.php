<?php

namespace App\Filament\App\Clusters\CRM\Resources\CompanyLeadResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\CompanyLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyLead extends ViewRecord
{
    protected static string $resource = CompanyLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
