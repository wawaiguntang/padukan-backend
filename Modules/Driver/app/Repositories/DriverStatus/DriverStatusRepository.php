<?php

namespace Modules\Driver\Repositories\DriverStatus;

use Modules\Driver\Models\DriverAvailabilityStatus;

/**
 * Driver Status Repository Implementation
 *
 * This class handles all driver status-related database operations
 * for the driver module with caching support.
 */
class DriverStatusRepository implements IDriverStatusRepository
{
    /**
     * The DriverAvailabilityStatus model instance
     *
     * @var DriverAvailabilityStatus
     */
    protected DriverAvailabilityStatus $model;


    /**
     * Constructor
     *
     * @param DriverAvailabilityStatus $model The DriverAvailabilityStatus model instance
     */
    public function __construct(DriverAvailabilityStatus $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findByProfileId(string $profileId): ?DriverAvailabilityStatus
    {
        return $this->model->where('profile_id', $profileId)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?DriverAvailabilityStatus
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): DriverAvailabilityStatus
    {
        $driverStatus = $this->model->create($data);

        // Cache operations disabled

        return $driverStatus;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $driverStatus = $this->model->find($id); // Don't use cached version for updates

        if (!$driverStatus) {
            return false;
        }

        $result = $driverStatus->update($data);

        // Cache operations disabled

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function updateByProfileId(string $profileId, array $data): bool
    {
        $driverStatus = $this->findByProfileId($profileId); // Don't use cached version for updates

        if (!$driverStatus) {
            return false;
        }

        return $this->update($driverStatus->id, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $driverStatus = $this->model->find($id); // Don't use cached version for deletes

        if (!$driverStatus) {
            return false;
        }

        $result = $driverStatus->delete();

        // Cache operations disabled

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function existsByProfileId(string $profileId): bool
    {
        return $this->model->where('profile_id', $profileId)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function updateOnlineStatus(string $profileId, string $onlineStatus): bool
    {
        return $this->updateByProfileId($profileId, ['online_status' => $onlineStatus]);
    }

    /**
     * {@inheritDoc}
     */
    public function updateOperationalStatus(string $profileId, string $operationalStatus): bool
    {
        return $this->updateByProfileId($profileId, ['operational_status' => $operationalStatus]);
    }

    /**
     * {@inheritDoc}
     */
    public function updateActiveService(string $profileId, ?string $activeService): bool
    {
        return $this->updateByProfileId($profileId, ['active_service' => $activeService]);
    }

    /**
     * {@inheritDoc}
     */
    public function updateLocation(string $profileId, ?float $latitude, ?float $longitude): bool
    {
        return $this->updateByProfileId($profileId, [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'last_updated_at' => now(),
        ]);
    }
}
