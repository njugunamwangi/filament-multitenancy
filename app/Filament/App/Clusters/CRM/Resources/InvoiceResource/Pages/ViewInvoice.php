<?php

namespace App\Filament\App\Clusters\CRM\Resources\InvoiceResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
