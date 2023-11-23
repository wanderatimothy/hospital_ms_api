<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medication>
 */
class MedicationFactory extends Factory
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
            'code' => $this->faker->unique()->text(10),
            'title' => $this->faker->text(50),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            "created_by" =>  $user->id,
            "last_modified_by" => $user->id
        ];
    }
}
