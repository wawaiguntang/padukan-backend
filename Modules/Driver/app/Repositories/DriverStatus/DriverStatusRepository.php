<?php

namespace Modules\Driver\Repositories\DriverStatus;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Driver\Models\DriverAvailabilityStatus;
use Modules\Driver\Cache\KeyManager\IKeyManager;

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
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * The cache key manager instance
     *
     * @var IKeyManager
     */
    protected IKeyManager $cacheKeyManager;

    /**
     * Cache TTL in seconds (5 minutes - shorter for status data)
     *
     * @var int
     */
    protected int $cacheTtl = 300;

    /**
     * Constructor
     *
     * @param DriverAvailabilityStatus $model The DriverAvailabilityStatus model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(DriverAvailabilityStatus $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findByProfileId(string $profileId): ?DriverAvailabilityStatus
    {
        $cacheKey = $this->cacheKeyManager::driverStatusByProfileId($profileId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return $this->model->where('profile_id', $profileId)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?DriverAvailabilityStatus
    {
        $cacheKey = $this->cacheKeyManager::driverStatusById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): DriverAvailabilityStatus
    {
        $driverStatus = $this->model->create($data);

        // Cache invalidation is handled by DriverStatusObserver

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

        // Cache invalidation is handled by DriverStatusObserver

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

        // Cache invalidation is handled by DriverStatusObserver

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
