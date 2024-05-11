<?php

namespace App\Filament\App\Clusters\Banking\Resources\AccountResource\Pages;

use App\Filament\App\Clusters\Banking\Resources\AccountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;
}
