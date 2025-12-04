<?php

namespace Modules\Setting\Policies\MerchantManagement;

use App\Shared\Setting\Services\ISettingService;

class MerchantManagementPolicy implements IMerchantManagementPolicy
{
    private ISettingService $settingService;
    private array $policySettings;

    public function __construct(ISettingService $settingService)
    {
        $this->settingService = $settingService;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $this->policySettings = $this->settingService->getSettingByKey('merchant.management')['value'] ?? [
            'max_merchants_per_profile_unverified' => 1,
            'max_merchants_per_profile_verified' => 10,
            'require_profile_verification' => true,
        ];
    }

    /**
     * Check if profile can create more merchants
     */
    public function canCreateMerchant(string $profileId): bool
    {
        // This would need access to profile and merchant repositories
        // For now, return true - implementation would check current count vs max
        return true;
    }

    /**
     * Get maximum merchants per profile
     */
    public function getMaxMerchantsPerProfile(string $profileId): int
    {
        // This would need to check if profile is verified
        // For now, return default values
        return $this->policySettings['max_merchants_per_profile_verified'] ?? 10;
    }

    /**
     * Check if merchant creation is allowed based on profile verification
     */
    public function canCreateMerchantBasedOnVerification(string $profileId): bool
    {
        // This would need to check profile verification status
        // For now, return true
        return true;
    }

    /**
     * Validate merchant creation requirements
     */
    public function validateMerchantCreation(string $profileId, array $merchantData): array
    {
        $errors = [];

        // Check if profile can create more merchants
        if (!$this->canCreateMerchant($profileId)) {
            $errors[] = 'Maximum number of merchants reached for this profile';
        }

        // Check verification requirements
        if (!$this->canCreateMerchantBasedOnVerification($profileId)) {
            $errors[] = 'Profile verification required to create merchants';
        }

        return $errors;
    }
}
