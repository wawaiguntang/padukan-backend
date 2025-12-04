<?php

namespace Modules\Merchant\Policies\ProfileOwnership;

use Modules\Merchant\Repositories\Profile\IProfileRepository;

class ProfileOwnershipPolicy implements IProfileOwnershipPolicy
{
    private IProfileRepository $profileRepository;

    public function __construct(IProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Check if user owns the profile
     */
    public function ownsProfile(string $userId, string $profileId): bool
    {
        $profile = $this->profileRepository->findById($profileId);

        return $profile && $profile->user_id === $userId;
    }
}
