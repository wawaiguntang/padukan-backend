<?php

namespace Modules\Driver\Observers;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Driver\Cache\KeyManager\IKeyManager;
use Modules\Driver\Models\DriverAvailabilityStatus;

/**
 * Driver Status Model Observer
 *
 * Handles cache management for DriverAvailabilityStatus model events
 */
class DriverStatusObserver
{
    /**
     * Cache repository instance
     */
    protected Cache $cache;

    /**
     * Cache key manager instance
     */
    protected IKeyManager $keyManager;

    /**
     * Cache TTL in seconds (5 minutes - frequently updated)
     */
    protected int $cacheTtl = 300;

    /**
     * Constructor
     */
    public function __construct(Cache $cache, IKeyManager $keyManager)
    {
        $this->cache = $cache;
        $this->keyManager = $keyManager;
    }

    /**
     * Handle the DriverAvailabilityStatus "created" event
     */
    public function created(DriverAvailabilityStatus $driverStatus): void
    {
        $this->cacheDriverStatusData($driverStatus);
    }

    /**
     * Handle the DriverAvailabilityStatus "updated" event
     */
    public function updated(DriverAvailabilityStatus $driverStatus): void
    {
        $this->invalidateDriverStatusCaches($driverStatus);
        $this->cacheDriverStatusData($driverStatus);
    }

    /**
     * Handle the DriverAvailabilityStatus "deleted" event
     */
    public function deleted(DriverAvailabilityStatus $driverStatus): void
    {
        $this->invalidateDriverStatusCaches($driverStatus);
    }

    /**
     * Cache driver status data in multiple cache keys
     */
    protected function cacheDriverStatusData(DriverAvailabilityStatus $driverStatus): void
    {
        // Cache by profile ID (most commonly accessed)
        $this->cache->put(
            $this->keyManager::driverStatusByProfileId($driverStatus->profile_id),
            $driverStatus,
            $this->cacheTtl
        );

        // Cache by driver status ID
        $this->cache->put(
            $this->keyManager::driverStatusById($driverStatus->id),
            $driverStatus,
            $this->cacheTtl
        );
    }

    /**
     * Invalidate all cache keys related to a driver status
     */
    protected function invalidateDriverStatusCaches(DriverAvailabilityStatus $driverStatus): void
    {
        // Invalidate by profile ID
        $this->cache->forget($this->keyManager::driverStatusByProfileId($driverStatus->profile_id));

        // Invalidate by driver status ID
        $this->cache->forget($this->keyManager::driverStatusById($driverStatus->id));
    }
}
