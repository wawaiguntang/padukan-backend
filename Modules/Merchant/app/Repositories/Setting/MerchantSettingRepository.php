<?php

namespace Modules\Merchant\Repositories\Setting;

use Modules\Merchant\Models\MerchantSetting;

/**
 * Merchant Setting Repository Implementation
 *
 * Handles merchant setting data operations
 */
class MerchantSettingRepository implements IMerchantSettingRepository
{
    public function __construct() {}

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
        return MerchantSetting::where('merchant_id', $merchantId)->first();
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

        return $setting->update($data);
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

        return $setting->delete();
    }
}
