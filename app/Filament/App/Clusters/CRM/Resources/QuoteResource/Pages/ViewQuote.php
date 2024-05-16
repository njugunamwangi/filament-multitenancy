<?php

namespace App\Filament\App\Clusters\CRM\Resources\QuoteResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
