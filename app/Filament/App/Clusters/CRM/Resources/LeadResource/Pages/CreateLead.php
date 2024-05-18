<?php

namespace App\Filament\App\Clusters\CRM\Resources\LeadResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\LeadResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

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
