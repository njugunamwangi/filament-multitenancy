<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {

            $panelSwitch->simple()
                ->visible(fn (): bool => auth()->user()->hasRole(Role::ADMIN));
        });
    }
}
