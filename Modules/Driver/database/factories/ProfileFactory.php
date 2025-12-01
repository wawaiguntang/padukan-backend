<?php

namespace Modules\Driver\Database\Factories;

use Modules\Driver\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Driver\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => Str::uuid(), // Will be overridden when creating with user
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'avatar' => $this->faker->imageUrl(200, 200, 'people'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'language' => 'id',
            'is_verified' => false,
            'verification_status' => 'pending',
            'verified_services' => [],
        ];
    }

    /**
     * Indicate that the profile is verified.
     */
    public function verified(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_services' => ['ride', 'food', 'send'],
        ]);
    }

    /**
     * Indicate that the profile has specific verified services.
     */
    public function withVerifiedServices(array $services): static
    {
        return $this->state(fn(array $attributes) => [
            'verified_services' => $services,
        ]);
    }
}
