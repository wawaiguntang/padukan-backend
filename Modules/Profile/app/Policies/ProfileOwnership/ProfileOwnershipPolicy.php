<?php

namespace Modules\Profile\Policies\ProfileOwnership;

use Modules\Profile\Repositories\Profile\IProfileRepository;
use Modules\Authorization\Repositories\Policy\IPolicyRepository;

class ProfileOwnershipPolicy implements IProfileOwnershipPolicy
{
    private IProfileRepository $profileRepository;
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(
        IProfileRepository $profileRepository,
        IPolicyRepository $policyRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('profile.ownership');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'enabled' => true,
                'strict_ownership' => true,
                'check_user_active' => true,
            ];
        }
    }

    /**
     * Check if user owns the profile
     */
    public function ownsProfile(string $userId, string $profileId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true; // If policy disabled, allow all
        }

        $profile = $this->profileRepository->findById($profileId);

        if (!$profile) {
            return false;
        }

        // Check if profile belongs to user
        if ($profile->user_id !== $userId) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can access profile data
     */
    public function canAccessProfile(string $userId, string $profileId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        // For now, access is same as ownership
        // In future, could add admin access here
        return $this->ownsProfile($userId, $profileId);
    }

    /**
     * Check if user can modify profile data
     */
    public function canModifyProfile(string $userId, string $profileId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        // For now, modification is same as ownership
        // In future, could add role-based modification rules
        return $this->ownsProfile($userId, $profileId);
    }
}