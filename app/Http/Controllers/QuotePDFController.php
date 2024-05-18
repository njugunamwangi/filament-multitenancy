<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use App\Models\Quote;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Facades\Invoice as FacadesInvoice;
use LasseRafn\Initials\Initials;

class QuotePDFController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Quote $quote, Request $request)
    {
        $customer = new Buyer([
            'name' => $quote->customer->name,
            'custom_fields' => [
                'email' => $quote->customer->email,
                'phone' => $quote->customer->phone,
            ],
        ]);

        $bank = Account::query()
            ->where('company_id', $quote->company->id)
            ->where('enabled', true)
            ->first();

        $seller = new Party([
            'name' => $quote->company->name,
            'phone' => $quote->company->phone,
            'email' => $quote->company->email,
            'custom_fields' => [
                'SWIFT' => $bank?->bic_swift_code,
                'Bank' => $bank?->bank_name,
                'Bank A/c No.' => $bank?->number,
            ],
        ]);

        $items = [];

        foreach ($quote->items as $item) {
            $items[] = (new InvoiceItem())
                ->title($item['description'])
                ->pricePerUnit($item['unit_price'])
                ->subTotalPrice($item['unit_price'] * $item['quantity'])
                ->quantity($item['quantity']);
        }

        $quotePdf = FacadesInvoice::make()
            ->buyer($customer)
            ->seller($seller)
            ->taxRate($quote->taxes)
            ->name('Quote')
            ->filename($quote->serial)
            ->logo(empty(Company::find($quote->company->id)->logo_id) ? '' : storage_path('/app/public/'.Company::find($quote->company->id)->logo->path))
            ->series((new Initials)->name($quote->company->name)->length(str_word_count($quote->company->name))->generate())
            ->sequence($quote->serial_number)
            ->delimiter('-')
            ->addItems($items)
            ->currencyCode($quote->currency->abbr)
            ->currencySymbol($quote->currency->symbol)
            ->currencyDecimals($quote->currency->precision)
            ->currencyDecimalPoint($quote->currency->decimal_mark)
            ->currencyThousandsSeparator($quote->currency->thousands_separator)
            ->currencyFormat($quote->currency->symbol_first ? $quote->currency->symbol.' '.'{VALUE}' : '{VALUE}'.' '.$quote->currency->symbol)
            ->currencyFraction($quote->currency->subunit_name)
            ->notes($quote->notes);

        if ($request->has('preview')) {
            return $quotePdf->stream();
        }

        return $quotePdf->download();
    }
}
