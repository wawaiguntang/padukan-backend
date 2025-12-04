<?php

namespace Modules\Merchant\Repositories\Setting;

use Modules\Merchant\Models\MerchantSetting;

interface IMerchantSettingRepository
{
    /**
     * Create merchant settings
     */
    public function create(array $data): MerchantSetting;

    /**
     * Find settings by merchant ID
     */
    public function findByMerchantId(string $merchantId): ?MerchantSetting;

    /**
     * Update settings by merchant ID
     */
    public function updateByMerchantId(string $merchantId, array $data): bool;

    /**
     * Delete settings by merchant ID
     */
    public function deleteByMerchantId(string $merchantId): bool;
}
