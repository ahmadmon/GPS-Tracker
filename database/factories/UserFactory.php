<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => $this->generateUniquePhoneNumber(),
            'phone_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'user_type' => 0,
            'status' => rand(1, 100) <= 80 ? 1 : 0,
            'remember_token' => Str::random(10),
        ];
    }

    private function generateUniquePhoneNumber()
    {
        $uniquePhoneNumbers = [];

        while (count($uniquePhoneNumbers) < 60) {
            $randomNumber = '09' . rand(10000000, 99999999);
            if (!in_array($randomNumber, $uniquePhoneNumbers)) {
                $uniquePhoneNumbers[] = $randomNumber;
            }
        }

        return $uniquePhoneNumbers[array_rand($uniquePhoneNumbers)];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
