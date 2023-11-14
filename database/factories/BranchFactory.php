<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => $this->faker->unique()->words(random_int(1,3)),
            "address" => $this->faker->address(),
            "phone_number" => $this->faker->phoneNumber(),
            "email" => $this->faker->companyEmail(),
            "created_by" => 1,
            "last_modified_by" => 1
        ];
    }
}
