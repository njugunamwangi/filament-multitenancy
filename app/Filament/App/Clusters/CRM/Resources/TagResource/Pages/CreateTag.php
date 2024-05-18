<?php

namespace App\Filament\App\Clusters\CRM\Resources\TagResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\TagResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

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
