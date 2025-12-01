<?php

namespace Modules\Driver\Repositories\Vehicle;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Models\Vehicle;
use Modules\Driver\Cache\KeyManager\IKeyManager;

/**
 * Vehicle Repository Implementation
 *
 * This class handles all vehicle-related database operations
 * for the driver module with caching support.
 */
class VehicleRepository implements IVehicleRepository
{
    /**
     * The Vehicle model instance
     *
     * @var Vehicle
     */
    protected Vehicle $model;

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
     * Cache TTL in seconds (15 minutes - reasonable for vehicle data)
     *
     * @var int
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     *
     * @param Vehicle $model The Vehicle model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(Vehicle $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findByProfileId(string $profileId): Collection
    {
        $cacheKey = $this->cacheKeyManager::vehiclesByProfileId($profileId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return $this->model->where('driver_profile_id', $profileId)->get();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Vehicle
    {
        $vehicle = $this->model->create($data);

        // Cache invalidation is handled by VehicleObserver

        return $vehicle;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $vehicle = $this->model->find($id); // Don't use cached version for updates

        if (!$vehicle) {
            return false;
        }

        $result = $vehicle->update($data);

        // Cache invalidation is handled by VehicleObserver

        return $result;
    }
}
