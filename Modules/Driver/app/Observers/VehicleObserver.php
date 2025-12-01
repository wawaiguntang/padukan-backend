<?php

namespace Modules\Driver\Observers;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Driver\Cache\KeyManager\IKeyManager;
use Modules\Driver\Models\Vehicle;

/**
 * Vehicle Model Observer
 *
 * Handles cache management for Vehicle model events
 */
class VehicleObserver
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
     * Cache TTL in seconds (15 minutes)
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     */
    public function __construct(Cache $cache, IKeyManager $keyManager)
    {
        $this->cache = $cache;
        $this->keyManager = $keyManager;
    }

    /**
     * Handle the Vehicle "created" event
     */
    public function created(Vehicle $vehicle): void
    {
        // Invalidate profile vehicles cache since new vehicle was added
        $this->invalidateProfileVehiclesCache($vehicle->driver_profile_id);
    }

    /**
     * Handle the Vehicle "updated" event
     */
    public function updated(Vehicle $vehicle): void
    {
        // Get original profile ID before update
        $originalProfileId = $vehicle->getOriginal('driver_profile_id');

        // Invalidate caches
        $this->invalidateVehicleCaches($vehicle->id);
        $this->invalidateProfileVehiclesCache($originalProfileId);

        // If profile changed, invalidate new profile cache too
        if ($vehicle->driver_profile_id !== $originalProfileId) {
            $this->invalidateProfileVehiclesCache($vehicle->driver_profile_id);
        }
    }

    /**
     * Handle the Vehicle "deleted" event
     */
    public function deleted(Vehicle $vehicle): void
    {
        // Invalidate all related caches
        $this->invalidateVehicleCaches($vehicle->id);
        $this->invalidateProfileVehiclesCache($vehicle->driver_profile_id);
    }

    /**
     * Invalidate profile vehicles cache
     */
    protected function invalidateProfileVehiclesCache(string $profileId): void
    {
        $this->cache->forget($this->keyManager::vehiclesByProfileId($profileId));
    }

    /**
     * Invalidate all cache keys related to a vehicle
     */
    protected function invalidateVehicleCaches(string $vehicleId): void
    {
        $this->cache->forget($this->keyManager::vehicleById($vehicleId));
    }
}
