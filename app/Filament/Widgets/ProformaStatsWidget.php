<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Quote;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProformaStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('Quotes', Quote::query()->count())
                ->description('Total quotes generated')
                ->color('primary')
                ->descriptionIcon('heroicon-o-document-check'),
            Stat::make('Invoices', Invoice::query()->count())
                ->description('Total invoices generated')
                ->color('warning')
                ->descriptionIcon('heroicon-o-bolt'),
            Stat::make('Paid Invoices', Invoice::query()->where('status', InvoiceStatus::Paid->value)->count())
                ->description('Total invoices paid')
                ->color('success')
                ->descriptionIcon('heroicon-o-banknotes'),
        ];
    }
}
