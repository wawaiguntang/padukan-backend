<?php

namespace Modules\Merchant\Policies\MerchantOwnership;

use Modules\Merchant\Repositories\Merchant\IMerchantRepository;
use Modules\Merchant\Repositories\Profile\IProfileRepository;

class MerchantOwnershipPolicy implements IMerchantOwnershipPolicy
{
    private IMerchantRepository $merchantRepository;
    private IProfileRepository $profileRepository;

    public function __construct(
        IMerchantRepository $merchantRepository,
        IProfileRepository $profileRepository
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->profileRepository = $profileRepository;
    }

    /**
     * Check if user owns the merchant
     */
    public function ownsMerchant(string $userId, string $merchantId): bool
    {
        $merchant = $this->merchantRepository->findById($merchantId);
        if (!$merchant) {
            return false;
        }

        $profile = $this->profileRepository->findByUserId($userId);
        if (!$profile) {
            return false;
        }

        return $merchant->profile_id === $profile->id;
    }

    /**
     * Check if user owns the merchant through profile
     */
    public function ownsMerchantThroughProfile(string $userId, string $merchantId): bool
    {
        return $this->ownsMerchant($userId, $merchantId);
    }

    /**
     * Validate merchant ownership for operation
     */
    public function validateMerchantOwnership(string $userId, string $merchantId): bool
    {
        return $this->ownsMerchant($userId, $merchantId);
    }
}
