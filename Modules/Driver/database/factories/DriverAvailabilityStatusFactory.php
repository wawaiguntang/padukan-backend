<?php

namespace Modules\Driver\Database\Factories;

use Modules\Driver\Models\DriverAvailabilityStatus;
use Modules\Driver\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Driver\Models\DriverAvailabilityStatus>
 */
class DriverAvailabilityStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DriverAvailabilityStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'profile_id' => Profile::factory(),
            'online_status' => $this->faker->randomElement(['online', 'offline']),
            'operational_status' => $this->faker->randomElement(['available', 'on_order', 'rest', 'suspended']),
            'active_service' => $this->faker->randomElement(['food', 'ride', 'car', 'send', 'mart', null]),
            'latitude' => $this->faker->latitude(-11.0, 6.0), // Indonesia latitude range
            'longitude' => $this->faker->longitude(95.0, 141.0), // Indonesia longitude range
            'last_updated_at' => now(),
        ];
    }

    /**
     * Create an online driver status.
     */
    public function online(): static
    {
        return $this->state(fn(array $attributes) => [
            'online_status' => 'online',
            'operational_status' => 'available',
        ]);
    }

    /**
     * Create an offline driver status.
     */
    public function offline(): static
    {
        return $this->state(fn(array $attributes) => [
            'online_status' => 'offline',
        ]);
    }

    /**
     * Create a driver on order.
     */
    public function onOrder(): static
    {
        return $this->state(fn(array $attributes) => [
            'online_status' => 'online',
            'operational_status' => 'on_order',
        ]);
    }

    /**
     * Create a driver with specific active service.
     */
    public function withActiveService(string $service): static
    {
        return $this->state(fn(array $attributes) => [
            'active_service' => $service,
        ]);
    }

    /**
     * Create a driver at a specific location.
     */
    public function atLocation(float $lat, float $lng): static
    {
        return $this->state(fn(array $attributes) => [
            'latitude' => $lat,
            'longitude' => $lng,
        ]);
    }
}
