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
     * Note: Cache clearing is handled automatically by MerchantObserver
     */
    public function updateById(string $merchantId, array $data): bool
    {
        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            return false;
        }

        return $merchant->update($data);
    }

    /**
     * Delete merchant by ID
     * Note: Cache clearing is handled automatically by MerchantObserver
     */
    public function deleteById(string $merchantId): bool
    {
        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            return false;
        }

        return $merchant->delete();
    }

    /**
     * Count merchants by profile ID
     * Note: This method bypasses cache to ensure accurate counts for business logic
     */
    public function countByProfileId(string $profileId): int
    {
        return Merchant::where('profile_id', $profileId)->count();
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
