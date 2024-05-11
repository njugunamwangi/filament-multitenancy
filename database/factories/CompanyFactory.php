<?php

namespace Database\Factories;

use App\Enums\EntityType;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'currency_id' => fake()->randomElement(Currency::all()->pluck('id')),
            'user_id' => fake()->randomElement(User::all()->pluck('id')),
            'entity_type' => fake()->randomElement(EntityType::cases())->value
        ];
    }
}
