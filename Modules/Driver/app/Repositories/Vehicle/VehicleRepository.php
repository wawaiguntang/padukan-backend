<?php

namespace Modules\Driver\Repositories\Vehicle;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Enums\VehicleTypeEnum;
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
    public function findById(string $id): ?Vehicle
    {
        $cacheKey = $this->cacheKeyManager::vehicleById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
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

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $vehicle = $this->model->find($id); // Don't use cached version for deletes

        if (!$vehicle) {
            return false;
        }

        $result = $vehicle->delete();

        // Cache invalidation is handled by VehicleObserver

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function updateVerificationStatus(string $id, bool $isVerified, ?string $verificationStatus = null): bool
    {
        $data = ['is_verified' => $isVerified];

        if ($verificationStatus) {
            $data['verification_status'] = $verificationStatus;
        }

        return $this->update($id, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function findByTypeAndProfileId(string $profileId, VehicleTypeEnum $type): Collection
    {
        return $this->model
            ->where('driver_profile_id', $profileId)
            ->where('type', $type)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function existsById(string $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function countByProfileId(string $profileId): int
    {
        return $this->model->where('driver_profile_id', $profileId)->count();
    }
}
