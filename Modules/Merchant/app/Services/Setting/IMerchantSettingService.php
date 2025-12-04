<?php

namespace Modules\Merchant\Services\Setting;

use Modules\Merchant\Models\MerchantSetting;

interface IMerchantSettingService
{
    /**
     * Create default merchant settings
     */
    public function createDefaultSettings(string $merchantId): MerchantSetting;

    /**
     * Get settings by merchant ID
     */
    public function getSettingsByMerchantId(string $merchantId): ?MerchantSetting;

    /**
     * Update merchant settings
     */
    public function updateSettings(string $merchantId, array $data): bool;

    /**
     * Delete merchant settings
     */
    public function deleteSettings(string $merchantId): bool;
}
