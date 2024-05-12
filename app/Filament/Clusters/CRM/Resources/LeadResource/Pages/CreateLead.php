<?php

namespace App\Filament\Clusters\CRM\Resources\LeadResource\Pages;

use App\Filament\Clusters\CRM\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
}
