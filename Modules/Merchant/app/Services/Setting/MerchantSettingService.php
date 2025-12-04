<?php

namespace Modules\Merchant\Services\Setting;

use Modules\Merchant\Models\MerchantSetting;
use Modules\Merchant\Repositories\Setting\IMerchantSettingRepository;

/**
 * Merchant Setting Service Implementation
 *
 * Handles merchant setting business logic operations
 */
class MerchantSettingService implements IMerchantSettingService
{
    private IMerchantSettingRepository $merchantSettingRepository;

    public function __construct(IMerchantSettingRepository $merchantSettingRepository)
    {
        $this->merchantSettingRepository = $merchantSettingRepository;
    }

    /**
     * Create default merchant settings
     */
    public function createDefaultSettings(string $merchantId): MerchantSetting
    {
        $defaultSettings = [
            'merchant_id' => $merchantId,
            'delivery_enabled' => true,
            'delivery_radius_km' => 10,
            'minimum_order_amount' => 25000, // 25,000 IDR
            'auto_accept_orders' => false,
            'preparation_time_minutes' => 15,
            'notifications_enabled' => true,
        ];

        return $this->merchantSettingRepository->create($defaultSettings);
    }

    /**
     * Get settings by merchant ID
     */
    public function getSettingsByMerchantId(string $merchantId): ?MerchantSetting
    {
        return $this->merchantSettingRepository->findByMerchantId($merchantId);
    }

    /**
     * Update merchant settings
     */
    public function updateSettings(string $merchantId, array $data): bool
    {
        return $this->merchantSettingRepository->updateByMerchantId($merchantId, $data);
    }

    /**
     * Delete merchant settings
     */
    public function deleteSettings(string $merchantId): bool
    {
        return $this->merchantSettingRepository->deleteByMerchantId($merchantId);
    }
}
