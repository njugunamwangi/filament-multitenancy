<?php

namespace App\Filament\App\Clusters\CRM\Resources\QuoteResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\QuoteResource;
use App\Mail\SendQuote;
use App\Models\Quote;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use LasseRafn\Initials\Initials;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $company = Filament::getTenant();
        $data['company_id'] = $company->id;
        $data['subtotal'] = str_replace(',', '', $data['subtotal']);
        $data['total'] = str_replace(',', '', $data['total']);
        $data['serial_number'] = (Quote::query()->where('company_id', $company->id)->max('serial_number') ?? 0) + 1;
        $series = (new Initials)->name($company->name)->length(str_word_count($company->name))->generate();
        $data['serial'] = $series.'-'.str_pad($data['serial_number'], 5, '0', STR_PAD_LEFT);

        return $data;
    }

    protected function afterCreate(): void
    {
        $quote = $this->getRecord();

        if ($quote->mail) {

            $quote->savePdf();

            Mail::to($quote->customer->email)->send(new SendQuote($quote));

            Notification::make()
                ->warning()
                ->icon('heroicon-o-bolt')
                ->title('Quote mailed')
                ->body('Quote mailed to ' . $quote->customer->name)
                ->send();
        }
    }
}
