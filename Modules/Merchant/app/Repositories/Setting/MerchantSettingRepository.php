<?php

namespace Modules\Merchant\Repositories\Setting;

use Illuminate\Support\Facades\Cache;
use Modules\Merchant\Models\MerchantSetting;
use Modules\Merchant\Cache\KeyManager\IKeyManager;

/**
 * Merchant Setting Repository Implementation
 *
 * Handles merchant setting data operations with caching
 */
class MerchantSettingRepository implements IMerchantSettingRepository
{
    private IKeyManager $keyManager;
    private int $cacheTtl = 900; // 15 minutes

    public function __construct(IKeyManager $keyManager)
    {
        $this->keyManager = $keyManager;
    }

    /**
     * Create merchant settings
     */
    public function create(array $data): MerchantSetting
    {
        return MerchantSetting::create($data);
    }

    /**
     * Find settings by merchant ID
     */
    public function findByMerchantId(string $merchantId): ?MerchantSetting
    {
        $cacheKey = $this->keyManager::getMerchantSettingsByMerchantIdKey($merchantId);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($merchantId) {
            return MerchantSetting::where('merchant_id', $merchantId)->first();
        });
    }

    /**
     * Update settings by merchant ID
     */
    public function updateByMerchantId(string $merchantId, array $data): bool
    {
        $setting = MerchantSetting::where('merchant_id', $merchantId)->first();

        if (!$setting) {
            return false;
        }

        $updated = $setting->update($data);

        if ($updated) {
            // Clear cache
            $cacheKey = $this->keyManager::getMerchantSettingsByMerchantIdKey($merchantId);
            Cache::forget($cacheKey);
        }

        return $updated;
    }

    /**
     * Delete settings by merchant ID
     */
    public function deleteByMerchantId(string $merchantId): bool
    {
        $setting = MerchantSetting::where('merchant_id', $merchantId)->first();

        if (!$setting) {
            return false;
        }

        $deleted = $setting->delete();

        if ($deleted) {
            // Clear cache
            $cacheKey = $this->keyManager::getMerchantSettingsByMerchantIdKey($merchantId);
            Cache::forget($cacheKey);
        }

        return $deleted;
    }
}
