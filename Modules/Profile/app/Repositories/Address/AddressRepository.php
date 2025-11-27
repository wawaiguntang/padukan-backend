<?php

namespace Modules\Profile\Repositories\Address;

use Modules\Profile\Models\Address;
use Modules\Profile\Cache\KeyManager\IKeyManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\DB;

/**
 * Address Repository Implementation
 */
class AddressRepository implements IAddressRepository
{
    protected Address $model;
    protected Cache $cache;
    protected IKeyManager $cacheKeyManager;
    protected int $cacheTtl = 900; // 15 minutes

    public function __construct(Address $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    public function findById(string $id): ?Address
    {
        return $this->model->find($id);
    }

    public function getByProfileId(string $profileId): Collection
    {
        $cacheKey = $this->cacheKeyManager::addressesByProfileId($profileId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return $this->model->where('profile_id', $profileId)->get();
        });
    }

    public function create(array $data): Address
    {
        $address = $this->model->create($data);

        // If this is set as primary, unset other primary addresses
        if ($address->is_primary) {
            $this->model->where('profile_id', $address->profile_id)
                       ->where('id', '!=', $address->id)
                       ->update(['is_primary' => false]);
        }

        $this->invalidateProfileAddressesCache($address->profile_id);

        return $address;
    }

    public function update(string $id, array $data): bool
    {
        $address = $this->model->find($id);

        if (!$address) {
            return false;
        }

        $oldProfileId = $address->profile_id;

        $result = $address->update($data);

        if ($result) {
            $address->refresh();

            // Handle primary address logic
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->model->where('profile_id', $address->profile_id)
                           ->where('id', '!=', $address->id)
                           ->update(['is_primary' => false]);
            }

            $this->invalidateProfileAddressesCache($oldProfileId);
            if ($oldProfileId !== $address->profile_id) {
                $this->invalidateProfileAddressesCache($address->profile_id);
            }
        }

        return $result;
    }

    public function delete(string $id): bool
    {
        $address = $this->model->find($id);

        if (!$address) {
            return false;
        }

        $profileId = $address->profile_id;
        $result = $address->delete();

        if ($result) {
            $this->invalidateProfileAddressesCache($profileId);
        }

        return $result;
    }

    public function setAsPrimary(string $id, string $profileId): bool
    {
        DB::transaction(function () use ($id, $profileId) {
            // Unset all primary addresses for this profile
            $this->model->where('profile_id', $profileId)
                       ->update(['is_primary' => false]);

            // Set this address as primary
            $this->model->where('id', $id)
                       ->where('profile_id', $profileId)
                       ->update(['is_primary' => true]);
        });

        $this->invalidateProfileAddressesCache($profileId);

        return true;
    }

    public function getPrimaryAddress(string $profileId): ?Address
    {
        return $this->model->where('profile_id', $profileId)
                          ->where('is_primary', true)
                          ->first();
    }

    protected function invalidateProfileAddressesCache(string $profileId): void
    {
        $this->cache->forget($this->cacheKeyManager::addressesByProfileId($profileId));
    }
}