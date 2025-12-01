<?php

namespace Modules\Customer\Repositories\Address;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Modules\Customer\Enums\AddressTypeEnum;
use Modules\Customer\Models\Address;
use Modules\Customer\Cache\KeyManager\IKeyManager;

/**
 * Address Repository Implementation
 *
 * This class handles all address-related database operations
 * for the customer module with caching support.
 */
class AddressRepository implements IAddressRepository
{
    /**
     * The Address model instance
     *
     * @var Address
     */
    protected Address $model;

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
     * Cache TTL in seconds (15 minutes - reasonable for address data)
     *
     * @var int
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     *
     * @param Address $model The Address model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(Address $model, Cache $cache, IKeyManager $cacheKeyManager)
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
        $cacheKey = $this->cacheKeyManager::addressesByProfileId($profileId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return $this->model->where('profile_id', $profileId)->get();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Address
    {
        $cacheKey = $this->cacheKeyManager::addressById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Address
    {
        $address = $this->model->create($data);

        // Cache invalidation is handled by AddressObserver

        return $address;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $address = $this->model->find($id);

        if (!$address) {
            return false;
        }

        $result = $address->update($data);

        // Cache invalidation is handled by AddressObserver

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $address = $this->model->find($id);

        if (!$address) {
            return false;
        }

        $result = $address->delete();

        // Cache invalidation is handled by AddressObserver

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function setAsPrimary(string $id): bool
    {
        $address = $this->model->find($id);

        if (!$address) {
            return false;
        }

        // First, set all addresses for this profile to non-primary
        $this->model->where('profile_id', $address->profile_id)->update(['is_primary' => false]);

        // Then set this address as primary
        $result = $address->update(['is_primary' => true]);

        // Cache invalidation is handled by AddressObserver

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findPrimaryByProfileId(string $profileId): ?Address
    {
        return $this->model
            ->where('profile_id', $profileId)
            ->where('is_primary', true)
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findByTypeAndProfileId(string $profileId, AddressTypeEnum $type): Collection
    {
        return $this->model
            ->where('profile_id', $profileId)
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
        return $this->model->where('profile_id', $profileId)->count();
    }
}
