<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentClass>
 */
class StudentClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $majors = ['RPL', 'DKV', 'TKR', 'AK', 'TPM', 'TL'];
        $class = ['X', 'XI', 'XII'];
        $class = $this->faker->randomElement($class);
        $major = $this->faker->randomElement($majors);
        $className = $class . ' ' . $major;

        return [
            'name' => $className,
        ];
    }
}
