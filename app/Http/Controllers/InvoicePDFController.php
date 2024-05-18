<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Facades\Invoice as FacadesInvoice;
use LasseRafn\Initials\Initials;

class InvoicePDFController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Invoice $invoice, Request $request)
    {
        $customer = new Buyer([
            'name' => $invoice->customer->name,
            'custom_fields' => [
                'email' => $invoice->customer->email,
                'phone' => $invoice->customer->phone,
            ],
        ]);

        $bank = Account::query()
            ->where('company_id', $invoice->company->id)
            ->where('enabled', true)
            ->first();

        $seller = new Party([
            'name' => $invoice->company->name,
            'phone' => $invoice->company->phone,
            'email' => $invoice->company->email,
            'custom_fields' => [
                'SWIFT' => $bank?->bic_swift_code,
                'Bank' => $bank?->bank_name,
                'Bank A/c No.' => $bank?->number,
            ],
        ]);

        $items = [];

        foreach ($invoice->items as $item) {
            $items[] = (new InvoiceItem())
                ->title($item['description'])
                ->pricePerUnit($item['unit_price'])
                ->subTotalPrice($item['unit_price'] * $item['quantity'])
                ->quantity($item['quantity']);
        }

        $invoicePdf = FacadesInvoice::make()
            ->buyer($customer)
            ->seller($seller)
            ->status($invoice->status->name)
            ->taxRate($invoice->taxes)
            ->name('Invoice')
            ->filename($invoice->serial)
            ->logo(empty(Company::find($invoice->company->id)->logo_id) ? '' : storage_path('/app/public/'.Company::find($invoice->company->id)->logo->path))
            ->series((new Initials)->name($invoice->company->name)->length(str_word_count($invoice->company->name))->generate())
            ->sequence($invoice->serial_number)
            ->delimiter('-')
            ->addItems($items)
            ->currencyCode($invoice->currency->abbr)
            ->currencySymbol($invoice->currency->symbol)
            ->currencyDecimals($invoice->currency->precision)
            ->currencyDecimalPoint($invoice->currency->decimal_mark)
            ->currencyThousandsSeparator($invoice->currency->thousands_separator)
            ->currencyFormat($invoice->currency->symbol_first ? $invoice->currency->symbol.' '.'{VALUE}' : '{VALUE}'.' '.$invoice->currency->symbol)
            ->currencyFraction($invoice->currency->subunit_name)
            ->notes($invoice->notes);

        if ($request->has('preview')) {
            return $invoicePdf->stream();
        }

        return $invoicePdf->download();
    }
}
