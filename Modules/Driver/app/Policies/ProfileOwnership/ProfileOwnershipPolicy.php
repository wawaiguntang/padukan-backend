<?php

namespace Modules\Driver\Policies\ProfileOwnership;

use Modules\Driver\Repositories\Profile\IProfileRepository;
use App\Shared\Setting\Services\ISettingService;

class ProfileOwnershipPolicy implements IProfileOwnershipPolicy
{
    private IProfileRepository $profileRepository;
    private ISettingService $settingService;
    private array $policySettings;

    public function __construct(
        IProfileRepository $profileRepository,
        ISettingService $settingService
    ) {
        $this->profileRepository = $profileRepository;
        $this->settingService = $settingService;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $this->policySettings = $this->settingService->getSettingByKey('driver.profile.ownership');
    }

    /**
     * Check if user owns the profile
     */
    public function ownsProfile(string $userId, string $profileId): bool
    {
        $profile = $this->profileRepository->findById($profileId);

        if (!$profile) {
            return false;
        }

        if ($profile->user_id !== $userId) {
            return false;
        }

        return true;
    }
}
