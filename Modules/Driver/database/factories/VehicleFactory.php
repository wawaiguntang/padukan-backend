<?php

namespace Modules\Driver\Database\Factories;

use Modules\Driver\Models\Vehicle;
use Modules\Driver\Models\Profile;
use Modules\Driver\Enums\VehicleTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Driver\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'driver_profile_id' => Profile::factory(),
            'type' => $this->faker->randomElement([VehicleTypeEnum::MOTORCYCLE, VehicleTypeEnum::CAR]),
            'brand' => $this->faker->randomElement(['Honda', 'Yamaha', 'Suzuki', 'Toyota', 'Mitsubishi']),
            'model' => $this->faker->word(),
            'year' => $this->faker->numberBetween(2010, 2024),
            'color' => $this->faker->colorName(),
            'license_plate' => strtoupper($this->faker->bothify('?? #### ??')),
            'is_verified' => false,
            'verification_status' => 'pending',
        ];
    }

    /**
     * Indicate that the vehicle is verified.
     */
    public function verified(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);
    }

    /**
     * Create a motorcycle.
     */
    public function motorcycle(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => VehicleTypeEnum::MOTORCYCLE,
            'brand' => $this->faker->randomElement(['Honda', 'Yamaha', 'Suzuki', 'Kawasaki']),
        ]);
    }

    /**
     * Create a car.
     */
    public function car(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => VehicleTypeEnum::CAR,
            'brand' => $this->faker->randomElement(['Toyota', 'Honda', 'Mitsubishi', 'Suzuki']),
        ]);
    }
}
