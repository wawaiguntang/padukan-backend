<?php

namespace Modules\Merchant\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Merchant\Models\Merchant;
use Modules\Merchant\Cache\KeyManager\IKeyManager;

/**
 * Merchant Observer
 *
 * Handles cache invalidation for merchant-related operations
 */
class MerchantObserver
{
    private IKeyManager $keyManager;

    public function __construct(IKeyManager $keyManager)
    {
        $this->keyManager = $keyManager;
    }

    /**
     * Handle the Merchant "created" event.
     */
    public function created(Merchant $merchant): void
    {
        // Clear cache for merchants by profile ID when a new merchant is created
        $cacheKeyByProfile = $this->keyManager::getMerchantsByProfileIdKey($merchant->profile_id);
        Cache::forget($cacheKeyByProfile);
    }

    /**
     * Handle the Merchant "updated" event.
     */
    public function updated(Merchant $merchant): void
    {
        // Clear cache for the specific merchant and merchants by profile ID
        $cacheKey = $this->keyManager::getMerchantByIdKey($merchant->id);
        Cache::forget($cacheKey);

        $cacheKeyByProfile = $this->keyManager::getMerchantsByProfileIdKey($merchant->profile_id);
        Cache::forget($cacheKeyByProfile);

        // If profile_id was changed, also clear cache for the old profile
        if ($merchant->wasChanged('profile_id')) {
            $oldProfileId = $merchant->getOriginal('profile_id');
            $cacheKeyOldProfile = $this->keyManager::getMerchantsByProfileIdKey($oldProfileId);
            Cache::forget($cacheKeyOldProfile);
        }
    }

    /**
     * Handle the Merchant "deleted" event.
     */
    public function deleted(Merchant $merchant): void
    {
        // Clear cache for the specific merchant and merchants by profile ID
        $cacheKey = $this->keyManager::getMerchantByIdKey($merchant->id);
        Cache::forget($cacheKey);

        $cacheKeyByProfile = $this->keyManager::getMerchantsByProfileIdKey($merchant->profile_id);
        Cache::forget($cacheKeyByProfile);
    }

    /**
     * Handle the Merchant "restored" event.
     */
    public function restored(Merchant $merchant): void
    {
        // Clear cache when merchant is restored (soft delete)
        $cacheKeyByProfile = $this->keyManager::getMerchantsByProfileIdKey($merchant->profile_id);
        Cache::forget($cacheKeyByProfile);
    }

    /**
     * Handle the Merchant "force deleted" event.
     */
    public function forceDeleted(Merchant $merchant): void
    {
        // Clear cache for the specific merchant and merchants by profile ID
        $cacheKey = $this->keyManager::getMerchantByIdKey($merchant->id);
        Cache::forget($cacheKey);

        $cacheKeyByProfile = $this->keyManager::getMerchantsByProfileIdKey($merchant->profile_id);
        Cache::forget($cacheKeyByProfile);
    }
}
