<?php

namespace Modules\Merchant\Repositories\Merchant;

use Illuminate\Support\Facades\Cache;
use Modules\Merchant\Models\Merchant;
use Modules\Merchant\Cache\KeyManager\IKeyManager;

/**
 * Merchant Repository Implementation
 *
 * Handles merchant data operations with caching
 */
class MerchantRepository implements IMerchantRepository
{
    private IKeyManager $keyManager;
    private int $cacheTtl = 900; // 15 minutes

    public function __construct(IKeyManager $keyManager)
    {
        $this->keyManager = $keyManager;
    }

    /**
     * Create a new merchant
     */
    public function create(array $data): Merchant
    {
        return Merchant::create($data);
    }

    /**
     * Find merchant by ID
     */
    public function findById(string $merchantId): ?Merchant
    {
        $cacheKey = $this->keyManager::getMerchantByIdKey($merchantId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($merchantId) {
            return Merchant::find($merchantId);
        });
    }

    /**
     * Find merchants by profile ID
     */
    public function findByProfileId(string $profileId)
    {
        $cacheKey = $this->keyManager::getMerchantsByProfileIdKey($profileId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return Merchant::where('profile_id', $profileId)->get();
        });
    }

    /**
     * Update merchant by ID
     */
    public function updateById(string $merchantId, array $data): bool
    {
        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            return false;
        }

        $updated = $merchant->update($data);

        if ($updated) {
            // Clear cache
            $cacheKey = $this->keyManager::getMerchantByIdKey($merchantId);
            Cache::forget($cacheKey);

            $cacheKeyByProfile = $this->keyManager::getMerchantsByProfileIdKey($merchant->profile_id);
            Cache::forget($cacheKeyByProfile);
        }

        return $updated;
    }

    /**
     * Delete merchant by ID
     */
    public function deleteById(string $merchantId): bool
    {
        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            return false;
        }

        $profileId = $merchant->profile_id;
        $deleted = $merchant->delete();

        if ($deleted) {
            // Clear cache
            $cacheKey = $this->keyManager::getMerchantByIdKey($merchantId);
            Cache::forget($cacheKey);

            $cacheKeyByProfile = $this->keyManager::getMerchantsByProfileIdKey($profileId);
            Cache::forget($cacheKeyByProfile);
        }

        return $deleted;
    }

    /**
     * Count merchants by profile ID
     */
    public function countByProfileId(string $profileId): int
    {
        return $this->findByProfileId($profileId)->count();
    }

    /**
     * Check if profile can create more merchants
     */
    public function canCreateMoreMerchants(string $profileId, int $maxMerchants): bool
    {
        $currentCount = $this->countByProfileId($profileId);
        return $currentCount < $maxMerchants;
    }
}
