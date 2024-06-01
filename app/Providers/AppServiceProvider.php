<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
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

        Filament::serving(function () {
            FilamentAsset::register([
                Css::make('custom-stylesheet', public_path('css/style.css'))->loadedOnRequest(),
                Css::make('font-stylesheet', public_path('fonts/font-awesome/css/font-awesome.min.css'))->loadedOnRequest(),
                Css::make('bootstrap-stylesheet', public_path('css/bootstrap.min.css'))->loadedOnRequest(),
            ]);
        });
    }
}
