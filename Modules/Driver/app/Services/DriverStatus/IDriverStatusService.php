<?php

namespace Modules\Driver\Services\DriverStatus;

use Modules\Driver\Models\DriverAvailabilityStatus;

/**
 * Driver Status Service Interface
 */
interface IDriverStatusService
{
    /**
     * Get or create driver status for profile
     */
    public function getOrCreateStatus(string $profileId): DriverAvailabilityStatus;

    /**
     * Update online status
     */
    public function updateOnlineStatus(string $profileId, string $onlineStatus): DriverAvailabilityStatus;

    /**
     * Update operational status
     */
    public function updateOperationalStatus(string $profileId, string $operationalStatus): DriverAvailabilityStatus;

    /**
     * Update active service
     */
    public function updateActiveService(string $profileId, ?string $activeService): DriverAvailabilityStatus;

    /**
     * Update location
     */
    public function updateLocation(string $profileId, float $latitude, float $longitude): DriverAvailabilityStatus;

    /**
     * Update multiple status fields at once
     */
    public function updateStatus(string $profileId, array $data): DriverAvailabilityStatus;
}
