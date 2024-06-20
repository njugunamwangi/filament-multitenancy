<?php

namespace App\Filament\App\Pages\Auth;

use App\Models\Role;
use Filament\Pages\Auth\Register as AuthRegister;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class Register extends AuthRegister
{
    protected function handleRegistration(array $data): Model
    {
        $user = $this->getUserModel()::create($data);

        $user->assignRole(Role::OWNER);

        return $user;
    }
}
