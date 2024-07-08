<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => $owner = 'Owner 1',
            'email' => Str::slug(str_replace(' ', '',$owner)).'@filament.test',
            'password' => bcrypt('Password'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole(Role::OWNER);
    }
}
