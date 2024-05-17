<?php

namespace App\Filament\App\Widgets;

use App\Models\Role;
use App\Models\Task;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Model;

class TaskChartWidget extends ChartWidget
{
    public ?Model $record = null;

    protected static ?int $sort = 3;

    public ?string $filter = 'thisMonth';

    protected static ?string $heading = 'Tasks';

    protected static ?string $maxHeight = '250px';

    protected static ?string $pollingInterval = '1s';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'This Week',
            'thisMonth' => 'This Month',
            'year' => 'This Year',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        $company = Filament::getTenant();

        $completed = Task::query()->where('company_id', $company->id)->where('is_completed', true);
        $incomplete = Task::query()->where('company_id', $company->id)->where('is_completed', false);

        match ($filter) {
            'week' => [
                $comp = Trend::query($completed)
                    ->dateColumn('due_date')
                    ->between(
                        start: now()->startOfWeek(),
                        end: now()->endOfWeek(),
                    )
                    ->perDay()
                    ->count(),
                $inc = Trend::query($incomplete)
                    ->dateColumn('due_date')
                    ->between(
                        start: now()->subWeek(),
                        end: now(),
                    )
                    ->perDay()
                    ->count(),
            ],
            'thisMonth' => [
                $comp = Trend::query($completed)
                    ->dateColumn('due_date')
                    ->between(
                        start: now()->startOfMonth(),
                        end: now()->endOfMonth(),
                    )
                    ->perDay()
                    ->count(),
                $inc = Trend::query($incomplete)
                    ->dateColumn('due_date')
                    ->between(
                        start: now()->subMonth(),
                        end: now(),
                    )
                    ->perDay()
                    ->count(),
            ],
            'year' => [
                $comp = Trend::query($completed)
                    ->dateColumn('due_date')
                    ->between(
                        start: now()->startOfYear(),
                        end: now()->endOfYear(),
                    )
                    ->perMonth()
                    ->count(),
                $inc = Trend::query($incomplete)
                    ->dateColumn('due_date')
                    ->between(
                        start: now()->startOfYear(),
                        end: now()->endOfYear(),
                    )
                    ->perMonth()
                    ->count(),
            ],
        };

        return [
            'datasets' => [
                [
                    'label' => 'Completed Tasks',
                    'data' => $comp->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'pointBackgroundColor' => 'rgb(54, 162, 235)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgb(54, 162, 235)',
                ],
                [
                    'label' => 'Incomplete Tasks',
                    'data' => $inc->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'pointBackgroundColor' => 'rgb(255, 99, 132)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgb(255, 99, 132)',
                ],
            ],
            'labels' => $comp->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
