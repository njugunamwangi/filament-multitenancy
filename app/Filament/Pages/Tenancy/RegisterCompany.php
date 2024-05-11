<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\Auth;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Company';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                // ...
            ]);
    }

    protected function handleRegistration(array $data): Company
    {
        $data['user_id'] = Auth::user()->id;

        $company = Company::create($data);

        return $company;
    }
}
