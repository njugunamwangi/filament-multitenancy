<?php

namespace App\Models;

use App\Casts\Money;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Facades\Invoice as FacadesInvoice;
use LasseRafn\Initials\Initials;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
         return [
             'subtotal' => Money::class,
             'total' => Money::class,
             'items' => 'json'
         ];
    }

    public function company(): BelongsTo
    {
         return $this->belongsTo(Company::class);
    }

    public function task(): BelongsTo
    {
         return $this->belongsTo(Task::class);
    }

    public function currency(): BelongsTo
    {
         return $this->belongsTo(Currency::class);
    }

    public function customer(): BelongsTo
    {
         return $this->belongsTo(Customer::class);
    }

    public function quote(): BelongsTo
    {
         return $this->belongsTo(Quote::class);
    }

    public function savePdf()
    {
        $company = Filament::getTenant();

        $customer = new Buyer([
            'name' => $this->customer->name,
            'custom_fields' => [
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
            ],
        ]);

        $bank = Account::query()
                    ->where('company_id' ,$company->id)
                    ->where('enabled', true)
                    ->first();

        $seller = new Party([
            'name'          => $company->name,
            'phone'         => $company->phone,
            'email'         => $company->email,
            'custom_fields' => [
                'SWIFT' => $bank?->bic_swift_code,
                'Bank' => $bank?->bank_name,
                'Bank A/c No.' => $bank?->number,
            ],
        ]);

        $items = [];

        foreach ($this->items as $item) {
            $items[] = (new InvoiceItem())
                ->title($item['description'])
                ->pricePerUnit($item['unit_price'])
                ->subTotalPrice($item['unit_price'] * $item['quantity'])
                ->quantity($item['quantity']);
        }

        FacadesInvoice::make()
            ->buyer($customer)
            ->seller($seller)
            ->taxRate($this->taxes)
            ->name('Invoice')
            ->filename($this->serial)
            ->logo(empty(Company::find($company->id)->logo_id) ? '' : storage_path('/app/public/'.Company::find($company->id)->logo->path))
            ->series((new Initials)->name($company->name)->length(str_word_count($company->name))->generate())
            ->sequence($this->serial_number)
            ->delimiter('-')
            ->addItems($items)
            ->currencyCode($this->currency->abbr)
            ->currencySymbol($this->currency->symbol)
            ->currencyDecimals($this->currency->precision)
            ->currencyDecimalPoint($this->currency->decimal_mark)
            ->currencyThousandsSeparator($this->currency->thousands_separator)
            ->currencyFormat($this->currency->symbol_first ? $this->currency->symbol.' '.'{VALUE}' : '{VALUE}'.' '.$this->currency->symbol)
            ->currencyFraction($this->currency->subunit_name)
            ->notes($this->notes)
            ->save('invoices');
    }
}
