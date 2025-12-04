<?php

namespace Modules\Merchant\Policies\MerchantOwnership;

interface IMerchantOwnershipPolicy
{
    /**
     * Check if user owns the merchant
     */
    public function ownsMerchant(string $userId, string $merchantId): bool;

    /**
     * Check if user owns the merchant through profile
     */
    public function ownsMerchantThroughProfile(string $userId, string $merchantId): bool;

    /**
     * Validate merchant ownership for operation
     */
    public function validateMerchantOwnership(string $userId, string $merchantId): bool;
}
