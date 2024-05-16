<?php

namespace App\Filament\App\Clusters\CRM\Resources\InvoiceResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\InvoiceResource;
use App\Mail\SendInvoice;
use App\Mail\SendQuote;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use LasseRafn\Initials\Initials;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $company = Filament::getTenant();
        $series = (new Initials)->name($company->name)->length(str_word_count($company->name))->generate();

        $data['company_id'] = $company->id;
        $data['subtotal'] = str_replace(',', '', $data['subtotal']);
        $data['total'] = str_replace(',', '', $data['total']);
        $data['serial_number'] = (Invoice::query()->where('company_id', $company->id)->max('serial_number') ?? 0) + 1;
        $data['serial'] = $series.'-'.str_pad($data['serial_number'], 5, '0', STR_PAD_LEFT);

        return $data;
    }

    protected function afterCreate(): void
    {
        $invoice = $this->getRecord();

        if ($invoice->mail) {

            $invoice->savePdf();

            Mail::to($invoice->customer->email)->send(new SendInvoice($invoice));

            Notification::make()
                ->success()
                ->icon('heroicon-o-bolt')
                ->title('Invoice mailed')
                ->body('Invoice mailed to ' . $invoice->customer->name)
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
