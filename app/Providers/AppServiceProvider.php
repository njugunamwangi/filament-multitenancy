<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
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

        FilamentAsset::register([
            Css::make('custom-stylesheet', public_path('invoices/css/style.css'))->loadedOnRequest(),
            Css::make('font-stylesheet', public_path('invoices/fonts/font-awesome/css/font-awesome.min.css'))->loadedOnRequest(),
            Css::make('bootstrap-stylesheet', public_path('invoices/css/bootstrap.min.css'))->loadedOnRequest(),
            Js::make('jquery', public_path('invoices/js/jquery.min.js'))->loadedOnRequest(),
            Js::make('jspdf', public_path('invoices/js/jspdf.min.js'))->loadedOnRequest(),
            Js::make('html2canvas', public_path('invoices/js/html2canvas.js'))->loadedOnRequest(),
            Js::make('js-app', public_path('invoices/js/app.js'))->loadedOnRequest(),
        ], package: 'app');
    }
}
