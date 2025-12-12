<?php

namespace Modules\Merchant\Repositories\Merchant;

use Modules\Merchant\Models\Merchant;

/**
 * Merchant Repository Implementation
 *
 * Handles merchant data operations
 */
class MerchantRepository implements IMerchantRepository
{
    public function __construct() {}

    /**
     * Create a new merchant
     */
    public function create(array $data): Merchant
    {
        return Merchant::create($data);
    }

    /**
     * Find merchant by ID
     */
    public function findById(string $merchantId): ?Merchant
    {
        return Merchant::find($merchantId);
    }

    /**
     * Find merchants by profile ID
     */
    public function findByProfileId(string $profileId)
    {
        return Merchant::where('profile_id', $profileId)->get();
    }

    /**
     * Update merchant by ID
     */
    public function updateById(string $merchantId, array $data): bool
    {
        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            return false;
        }

        return $merchant->update($data);
    }

    /**
     * Delete merchant by ID
     */
    public function deleteById(string $merchantId): bool
    {
        $merchant = Merchant::find($merchantId);

        if (!$merchant) {
            return false;
        }

        return $merchant->delete();
    }

    /**
     * Count merchants by profile ID
     */
    public function countByProfileId(string $profileId): int
    {
        return Merchant::where('profile_id', $profileId)->count();
    }

    /**
     * Check if profile can create more merchants
     */
    public function canCreateMoreMerchants(string $profileId, int $maxMerchants): bool
    {
        $currentCount = $this->countByProfileId($profileId);
        return $currentCount < $maxMerchants;
    }
}
