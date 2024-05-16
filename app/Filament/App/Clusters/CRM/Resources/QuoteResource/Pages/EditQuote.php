<?php

namespace App\Filament\App\Clusters\CRM\Resources\QuoteResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
