<?php

namespace Database\Factories;

use App\Models\User;
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
        $user = User::select(['id'])->orderBy('id','desc')->first();
        return [
            "name" => $this->faker->unique()->words(),
            "address" => $this->faker->address(),
            "phone_number" => $this->faker->phoneNumber(),
            "email" => $this->faker->companyEmail(),
            "created_by" =>  $user->id,
            "last_modified_by" => $user->id
        ];
    }
}
