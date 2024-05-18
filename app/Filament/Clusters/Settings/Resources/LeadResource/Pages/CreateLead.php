<?php

namespace App\Filament\Clusters\Settings\Resources\LeadResource\Pages;

use App\Filament\Clusters\Settings\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
}
