<?php

namespace App\Filament\App\Clusters\CRM\Resources\CompanyLeadResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\CompanyLeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyLead extends EditRecord
{
    protected static string $resource = CompanyLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
