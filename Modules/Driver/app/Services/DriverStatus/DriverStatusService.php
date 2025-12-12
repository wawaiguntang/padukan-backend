<?php

namespace Modules\Driver\Services\DriverStatus;

use Modules\Driver\Models\DriverAvailabilityStatus;
use Modules\Driver\Repositories\DriverStatus\IDriverStatusRepository;

/**
 * Driver Status Service Implementation
 */
class DriverStatusService implements IDriverStatusService
{
    /**
     * Driver status repository instance
     */
    protected IDriverStatusRepository $driverStatusRepository;


    /**
     * Constructor
     */
    public function __construct(
        IDriverStatusRepository $driverStatusRepository
    ) {
        $this->driverStatusRepository = $driverStatusRepository;
    }

    /**
     * Get or create driver status for profile
     */
    public function getOrCreateStatus(string $profileId): DriverAvailabilityStatus
    {
        $status = $this->driverStatusRepository->findByProfileId($profileId);

        if (!$status) {
            $status = $this->driverStatusRepository->create([
                'profile_id' => $profileId,
                'online_status' => 'offline',
                'operational_status' => 'available',
            ]);
        }

        return $status;
    }

    /**
     * Update online status
     */
    public function updateOnlineStatus(string $profileId, string $onlineStatus): DriverAvailabilityStatus
    {
        $status = $this->getOrCreateStatus($profileId);

        $this->driverStatusRepository->update($status->id, [
            'online_status' => $onlineStatus,
            'last_updated_at' => now(),
        ]);

        return $this->driverStatusRepository->findByProfileId($profileId);
    }

    /**
     * Update operational status
     */
    public function updateOperationalStatus(string $profileId, string $operationalStatus): DriverAvailabilityStatus
    {
        $status = $this->getOrCreateStatus($profileId);

        $this->driverStatusRepository->update($status->id, [
            'operational_status' => $operationalStatus,
            'last_updated_at' => now(),
        ]);

        return $this->driverStatusRepository->findByProfileId($profileId);
    }

    /**
     * Update active service
     */
    public function updateActiveService(string $profileId, ?string $activeService): DriverAvailabilityStatus
    {
        $status = $this->getOrCreateStatus($profileId);

        $this->driverStatusRepository->update($status->id, [
            'active_service' => $activeService,
            'last_updated_at' => now(),
        ]);

        return $this->driverStatusRepository->findByProfileId($profileId);
    }

    /**
     * Update location
     */
    public function updateLocation(string $profileId, float $latitude, float $longitude): DriverAvailabilityStatus
    {
        $status = $this->getOrCreateStatus($profileId);

        $this->driverStatusRepository->update($status->id, [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'last_updated_at' => now(),
        ]);

        return $this->driverStatusRepository->findByProfileId($profileId);
    }

    /**
     * Update multiple status fields at once
     */
    public function updateStatus(string $profileId, array $data): DriverAvailabilityStatus
    {
        $status = $this->getOrCreateStatus($profileId);

        $updateData = array_merge($data, ['last_updated_at' => now()]);

        $this->driverStatusRepository->update($status->id, $updateData);

        return $this->driverStatusRepository->findByProfileId($profileId);
    }
}
