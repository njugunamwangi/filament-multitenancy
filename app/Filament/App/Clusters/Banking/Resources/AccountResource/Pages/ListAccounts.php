<?php

namespace App\Filament\App\Clusters\Banking\Resources\AccountResource\Pages;

use App\Filament\App\Clusters\Banking\Resources\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
