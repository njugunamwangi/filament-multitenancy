<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => $company_id = fake()->randomElement(Company::all()->pluck('id')),
            'customer_id' => fake()->randomElement(Customer::query()->where('company_id', $company_id)->get()->pluck('id')),
            'description' => fake()->paragraph(),
            'due_date' => Carbon::now()->addDays(fake()->numberBetween(5, 30)),
            'is_completed' => fake()->randomElement([true, false]),
            'requires_equipment' => fake()->randomElement([true, false]),
        ];
    }
}
