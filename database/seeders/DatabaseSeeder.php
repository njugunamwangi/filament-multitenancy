<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BrandSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            CurrencySeeder::class,
            OwnerSeeder::class,
            CompanySeeder::class,
            LeadSeeder::class,
            CustomerSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
