<?php

namespace App\Filament\Clusters\Settings\Resources\CurrencyResource\Pages;

use App\Filament\Clusters\Settings\Resources\CurrencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
