<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
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
            'label' => $this->faker->word(),
            'purpose' => $this->faker->sentence(),
            'unique_number' => '_R'. $this->faker->unique()->randomNumber(5),
            "created_by" =>  $user->id,
            "last_modified_by" => $user->id
        ];
    }
}
