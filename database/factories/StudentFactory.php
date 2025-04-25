<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_class_id' => fake()->numberBetween(1, 10),
            'name' => fake()->name(),
            'nisn' => fake()->unique()->numberBetween(1000000000, 9999999999),
            'phone' => fake()->unique()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'gender' => fake()->randomElement(['laki-laki', 'perempuan']),
            'born_date' => fake()->date(),
            'height' => fake()->numberBetween(150, 200),
            'weight' => fake()->numberBetween(40, 100),
            'province' => fake()->state(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'is_graduated' => fake()->boolean(),
            'is_married' => fake()->boolean(),
            'password' => Hash::make('password'),
        ];
    }
}
