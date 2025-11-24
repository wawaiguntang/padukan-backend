<?php

namespace Modules\Authentication\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\Authentication\Enums\UserStatus;
use Modules\Authentication\Models\User;

/**
 * User Factory
 *
 * Factory for creating User model instances for testing
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'phone' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'status' => UserStatus::ACTIVE,
        ];
    }

    /**
     * Indicate that the user is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatus::PENDING,
        ]);
    }

    /**
     * Indicate that the user is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserStatus::SUSPEND,
        ]);
    }

    /**
     * Create user with only phone (no email).
     */
    public function phoneOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
        ]);
    }

    /**
     * Create user with only email (no phone).
     */
    public function emailOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => null,
        ]);
    }
}