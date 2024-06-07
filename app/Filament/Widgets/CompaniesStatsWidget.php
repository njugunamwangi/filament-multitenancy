<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompaniesStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getColumns(): int
    {
        return 2;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('Users on the system')
                ->color('success')
                ->descriptionIcon('heroicon-o-user-group'),
            Stat::make('Companies', Company::count())
                ->description('Companies using the system')
                ->color('primary')
                ->descriptionIcon('heroicon-o-building-office-2')
        ];
    }
}
