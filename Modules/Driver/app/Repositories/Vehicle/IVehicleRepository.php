<?php

namespace Modules\Driver\Repositories\Vehicle;

use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Enums\VehicleTypeEnum;
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
     * Find a vehicle by ID
     *
     * @param string $id The vehicle's UUID
     * @return Vehicle|null The vehicle model if found, null otherwise
     */
    public function findById(string $id): ?Vehicle;

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

    /**
     * Delete a vehicle
     *
     * @param string $id The vehicle's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Update vehicle verification status
     *
     * @param string $id The vehicle's UUID
     * @param bool $isVerified The verification status
     * @param string|null $verificationStatus The verification status
     * @return bool True if update was successful, false otherwise
     */
    public function updateVerificationStatus(string $id, bool $isVerified, ?string $verificationStatus = null): bool;

    /**
     * Find vehicles by type and profile ID
     *
     * @param string $profileId The profile's UUID
     * @param VehicleTypeEnum $type The vehicle type
     * @return Collection<Vehicle> Collection of vehicle models
     */
    public function findByTypeAndProfileId(string $profileId, VehicleTypeEnum $type): Collection;

    /**
     * Check if vehicle exists by ID
     *
     * @param string $id The vehicle's UUID
     * @return bool True if vehicle exists, false otherwise
     */
    public function existsById(string $id): bool;

    /**
     * Count vehicles by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return int Number of vehicles for the profile
     */
    public function countByProfileId(string $profileId): int;
}
