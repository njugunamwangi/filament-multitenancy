<?php

namespace App\Filament\App\Clusters\CRM\Resources\CompanyLeadResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\CompanyLeadResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyLead extends CreateRecord
{
    protected static string $resource = CompanyLeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
