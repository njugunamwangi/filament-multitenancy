<?php

namespace App\Filament\App\Clusters\CRM\Resources\CustomerResource\Pages;

use App\Filament\App\Clusters\CRM\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $company_id = Filament::getTenant()->id;

        return [
            'all' => Tab::make('All')
                ->badge(Customer::query()->where('company_id', $company_id)->count()),
            'archived' => Tab::make('Archived')
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed())
                ->badge(Customer::query()->where('company_id', $company_id)->onlyTrashed()->count()),
        ];
    }
}
