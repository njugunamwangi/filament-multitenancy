<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = ['Owner 1', 'Owner 2'];

        foreach($owners as $owner) {
            $user = User::create([
                'name' => $owner,
                'email' => Str::slug($owner) . '@drones.test',
                'password' => bcrypt('Password'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole(Role::OWNER);
        }
    }
}
