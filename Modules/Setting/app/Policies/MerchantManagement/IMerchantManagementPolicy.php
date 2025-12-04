<?php

namespace Modules\Setting\Policies\MerchantManagement;

interface IMerchantManagementPolicy
{
    /**
     * Check if profile can create more merchants
     */
    public function canCreateMerchant(string $profileId): bool;

    /**
     * Get maximum merchants per profile
     */
    public function getMaxMerchantsPerProfile(string $profileId): int;

    /**
     * Check if merchant creation is allowed based on profile verification
     */
    public function canCreateMerchantBasedOnVerification(string $profileId): bool;

    /**
     * Validate merchant creation requirements
     */
    public function validateMerchantCreation(string $profileId, array $merchantData): array;
}
