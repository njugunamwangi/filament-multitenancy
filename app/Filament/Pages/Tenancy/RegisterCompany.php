<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
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

        $recipients = User::role(Role::ADMIN)->get();

        foreach($recipients as $recipient)
        {
            Notification::make()
                ->title('New company')
                ->body($company->user->name . ' created company ' . $company->name)
                ->success()
                ->icon('heroicon-o-building-office-2')
                ->actions([
                    Action::make('read')
                        ->label('Mark as read')
                        ->markAsRead()
                ])
                ->sendToDatabase($recipient);
        }

        return $company;
    }
}
