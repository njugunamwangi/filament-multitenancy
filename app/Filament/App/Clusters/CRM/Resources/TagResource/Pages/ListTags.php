<?php

namespace App\Filament\App\Clusters\CRM\Resources\TagResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
