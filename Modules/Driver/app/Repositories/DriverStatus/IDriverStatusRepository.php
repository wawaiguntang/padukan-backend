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
     * Find driver status by ID
     *
     * @param string $id The driver status's UUID
     * @return DriverAvailabilityStatus|null The driver status model if found, null otherwise
     */
    public function findById(string $id): ?DriverAvailabilityStatus;

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
     * Delete a driver status
     *
     * @param string $id The driver status's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Check if driver status exists by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return bool True if driver status exists, false otherwise
     */
    public function existsByProfileId(string $profileId): bool;

    /**
     * Update online status
     *
     * @param string $profileId The profile's UUID
     * @param string $onlineStatus The online status
     * @return bool True if update was successful, false otherwise
     */
    public function updateOnlineStatus(string $profileId, string $onlineStatus): bool;

    /**
     * Update operational status
     *
     * @param string $profileId The profile's UUID
     * @param string $operationalStatus The operational status
     * @return bool True if update was successful, false otherwise
     */
    public function updateOperationalStatus(string $profileId, string $operationalStatus): bool;

    /**
     * Update active service
     *
     * @param string $profileId The profile's UUID
     * @param string|null $activeService The active service
     * @return bool True if update was successful, false otherwise
     */
    public function updateActiveService(string $profileId, ?string $activeService): bool;

    /**
     * Update location
     *
     * @param string $profileId The profile's UUID
     * @param float|null $latitude The latitude
     * @param float|null $longitude The longitude
     * @return bool True if update was successful, false otherwise
     */
    public function updateLocation(string $profileId, ?float $latitude, ?float $longitude): bool;
}
