<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bed>
 */
class BedFactory extends Factory
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
            'tag_name' => $this->faker->unique()->numberBetween(400,3400),
            'bed_status' => 'vacant',
            'room_id' => $this->faker->randomElement([1,2,3,4,5,6,7]),
            "created_by" =>  $user->id,
            "last_modified_by" => $user->id
        ];
    }
}
