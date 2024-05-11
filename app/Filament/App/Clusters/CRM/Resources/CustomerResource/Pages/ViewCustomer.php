<?php

namespace App\Filament\App\Clusters\CRM\Resources\CustomerResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
