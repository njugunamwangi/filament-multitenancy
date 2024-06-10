<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = Brand::all()->pluck('id');
        $companies = Company::all()->pluck('id');

        return [
            'brand_id' => $brands->random(),
            'registration' => '5Y-'. strtoupper(fake()->randomLetter() ). fake()->randomDigit() . strtoupper(fake()->randomLetter()) .fake()->randomDigit(),
            'company_id' => $companies->random(),
        ];
    }
}
