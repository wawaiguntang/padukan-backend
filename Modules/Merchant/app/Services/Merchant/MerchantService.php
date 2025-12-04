<?php

namespace Modules\Merchant\Services\Merchant;

use Modules\Merchant\Models\Merchant;
use Modules\Merchant\Repositories\Merchant\IMerchantRepository;
use Modules\Merchant\Repositories\Profile\IProfileRepository;

/**
 * Merchant Service Implementation
 *
 * Handles merchant business logic operations
 */
class MerchantService implements IMerchantService
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
     * Create a new merchant
     */
    public function createMerchant(string $profileId, array $data): Merchant
    {
        $data['profile_id'] = $profileId;

        // Generate slug from business name if not provided
        if (!isset($data['slug']) && isset($data['business_name'])) {
            $data['slug'] = $this->generateSlug($data['business_name']);
        }

        return $this->merchantRepository->create($data);
    }

    /**
     * Get merchant by ID
     */
    public function getMerchantById(string $merchantId): ?Merchant
    {
        return $this->merchantRepository->findById($merchantId);
    }

    /**
     * Get merchants by profile ID
     */
    public function getMerchantsByProfileId(string $profileId)
    {
        return $this->merchantRepository->findByProfileId($profileId);
    }

    /**
     * Update merchant information
     */
    public function updateMerchant(string $merchantId, array $data): bool
    {
        return $this->merchantRepository->updateById($merchantId, $data);
    }

    /**
     * Delete a merchant
     */
    public function deleteMerchant(string $merchantId): bool
    {
        return $this->merchantRepository->deleteById($merchantId);
    }

    /**
     * Check if profile can create more merchants
     */
    public function canCreateMerchant(string $profileId): bool
    {
        $profile = $this->profileRepository->findById($profileId);

        if (!$profile) {
            return false;
        }

        $currentCount = $this->merchantRepository->countByProfileId($profileId);
        return $currentCount < $profile->max_merchant;
    }

    /**
     * Update merchant status
     */
    public function updateStatus(string $merchantId, \Modules\Merchant\Enums\MerchantStatusEnum $status): bool
    {
        return $this->updateMerchant($merchantId, ['status' => $status]);
    }

    /**
     * Update merchant verification status
     */
    public function updateVerificationStatus(string $merchantId, bool $isVerified, ?string $verificationStatus = null): bool
    {
        $data = ['is_verified' => $isVerified];

        if ($verificationStatus) {
            $data['verification_status'] = $verificationStatus;
        }

        return $this->updateMerchant($merchantId, $data);
    }

    /**
     * Generate a unique slug from business name
     */
    private function generateSlug(string $businessName): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($businessName);
        $slug = $baseSlug;
        $counter = 1;

        while (Merchant::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
