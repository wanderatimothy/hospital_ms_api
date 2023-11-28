<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentType>
 */
class DocumentTypeFactory extends Factory
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
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'branch_id' => 1,
            "created_by" =>  $user->id,
            "last_modified_by" => $user->id
        ];
    }
}
