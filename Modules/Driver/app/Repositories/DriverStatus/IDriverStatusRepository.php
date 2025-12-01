<?php

namespace Modules\Driver\Repositories\DriverStatus;

use Modules\Driver\Models\DriverAvailabilityStatus;

/**
 * Interface for Driver Status Repository
 *
 * This interface defines the contract for driver status data operations
 * in the driver module.
 */
interface IDriverStatusRepository
{
    /**
     * Find driver status by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return DriverAvailabilityStatus|null The driver status model if found, null otherwise
     */
    public function findByProfileId(string $profileId): ?DriverAvailabilityStatus;

    /**
     * Create a new driver status
     *
     * @param array $data Driver status data containing:
     * - profile_id: string - Profile's UUID
     * - online_status?: string - Online status (default: offline)
     * - operational_status?: string - Operational status (default: available)
     * - active_service?: string - Current active service
     * - latitude?: float - Current latitude
     * - longitude?: float - Current longitude
     * @return DriverAvailabilityStatus The created driver status model
     */
    public function create(array $data): DriverAvailabilityStatus;

    /**
     * Update an existing driver status
     *
     * @param string $id The driver status's UUID
     * @param array $data Driver status data to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;

    /**
     * Update driver status by profile ID
     *
     * @param string $profileId The profile's UUID
     * @param array $data Driver status data to update
     * @return bool True if update was successful, false otherwise
     */
    public function updateByProfileId(string $profileId, array $data): bool;

    /**
     * Check if driver status exists by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return bool True if driver status exists, false otherwise
     */
    public function existsByProfileId(string $profileId): bool;
}
