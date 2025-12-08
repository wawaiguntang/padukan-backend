<?php

namespace App\Shared\Merchant\Services;

interface IMerchantService
{
    /**
     * Check if user owns a merchant
     *
     * @param string $userId
     * @param string $merchantId
     * @return bool
     */
    public function checkOwnership(string $userId, string $merchantId): bool;

    /**
     * Get merchant by ID
     *
     * @param string $id
     * @return array|null
     */
    public function getMerchantById(string $id): ?array;

    /**
     * Get merchant settings
     *
     * @param string $merchantId
     * @return array|null
     */
    public function getMerchantSetting(string $merchantId): ?array;

    /**
     * Get merchant schedule
     *
     * @param string $merchantId
     * @return array|null
     */
    public function getMerchantSchedule(string $merchantId): ?array;
}
