<?php

namespace Modules\Driver\Services\Vehicle;

use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Enums\VehicleTypeEnum;
use Modules\Driver\Models\Vehicle;

/**
 * Interface for Vehicle Service
 *
 * This interface defines the contract for vehicle business logic operations
 * in the driver module.
 */
interface IVehicleService
{
    /**
     * Create a new vehicle for a driver profile
     *
     * @param string $userId The user's UUID
     * @param array $data Vehicle data
     * @return Vehicle The created vehicle model
     */
    public function createVehicle(string $userId, array $data): Vehicle;

    /**
     * Get vehicles by user ID
     *
     * @param string $userId The user's UUID
     * @return Collection<Vehicle> Collection of vehicle models
     */
    public function getVehiclesByUserId(string $userId): Collection;

    /**
     * Get vehicle by ID
     *
     * @param string $vehicleId The vehicle's UUID
     * @return Vehicle|null The vehicle model if found, null otherwise
     */
    public function getVehicleById(string $vehicleId): ?Vehicle;

    /**
     * Update vehicle information
     *
     * @param string $vehicleId The vehicle's UUID
     * @param array $data Vehicle data to update
     * @return bool True if update was successful, false otherwise
     */
    public function updateVehicle(string $vehicleId, array $data): bool;

    /**
     * Update vehicle verification status
     *
     * @param string $vehicleId The vehicle's UUID
     * @param bool $isVerified The verification status
     * @param string|null $verificationStatus The verification status
     * @return bool True if update was successful, false otherwise
     */
    public function updateVerificationStatus(string $vehicleId, bool $isVerified, ?string $verificationStatus = null): bool;

    /**
     * Delete a vehicle
     *
     * @param string $vehicleId The vehicle's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteVehicle(string $vehicleId): bool;
}
