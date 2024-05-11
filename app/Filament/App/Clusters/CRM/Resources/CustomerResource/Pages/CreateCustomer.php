<?php

namespace App\Filament\App\Clusters\CRM\Resources\CustomerResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
