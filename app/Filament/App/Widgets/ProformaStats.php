<?php

namespace App\Filament\App\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Quote;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProformaStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $company = Filament::getTenant();

        return [
            Stat::make('Quotes', Quote::query()->where('company_id', $company->id)->count())
                ->description('Total quotes generated')
                ->color('primary')
                ->descriptionIcon('heroicon-o-document-check'),
            Stat::make('Invoices', Invoice::query()->where('company_id', $company->id)->count())
                ->description('Total invoices generated')
                ->color('warning')
                ->descriptionIcon('heroicon-o-bolt'),
            Stat::make('Paid Invoices', Invoice::query()->where('company_id', $company->id)->where('status', InvoiceStatus::Paid->value)->count())
                ->description('Total invoices paid')
                ->color('success')
                ->descriptionIcon('heroicon-o-banknotes'),
        ];
    }
}
