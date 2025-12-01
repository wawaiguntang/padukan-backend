<?php

namespace Modules\Driver\Repositories\Vehicle;

use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Models\Vehicle;

/**
 * Interface for Vehicle Repository
 *
 * This interface defines the contract for vehicle data operations
 * in the driver module.
 */
interface IVehicleRepository
{
    /**
     * Find vehicles by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return Collection<Vehicle> Collection of vehicle models
     */
    public function findByProfileId(string $profileId): Collection;

    /**
     * Create a new vehicle
     *
     * @param array $data Vehicle data containing:
     * - driver_profile_id: string - Profile's UUID
     * - type: VehicleTypeEnum - Vehicle type
     * - brand: string - Vehicle brand
     * - model: string - Vehicle model
     * - year: int - Manufacturing year
     * - color: string - Vehicle color
     * - license_plate: string - License plate number
     * - is_verified?: bool - Verification status (optional)
     * - verification_status?: string - Verification status (optional)
     * @return Vehicle The created vehicle model
     */
    public function create(array $data): Vehicle;

    /**
     * Update an existing vehicle
     *
     * @param string $id The vehicle's UUID
     * @param array $data Vehicle data to update (same structure as create)
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;
}
