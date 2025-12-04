<?php

namespace Modules\Merchant\Repositories\Merchant;

use Modules\Merchant\Models\Merchant;

/**
 * Interface for Merchant Repository
 *
 * This interface defines the contract for merchant data operations
 * in the merchant module.
 */
interface IMerchantRepository
{
    /**
     * Create a new merchant
     *
     * @param array $data Merchant data
     * @return Merchant The created merchant model
     */
    public function create(array $data): Merchant;

    /**
     * Find merchant by ID
     *
     * @param string $merchantId The merchant's UUID
     * @return Merchant|null The merchant model if found, null otherwise
     */
    public function findById(string $merchantId): ?Merchant;

    /**
     * Find merchants by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return \Illuminate\Database\Eloquent\Collection Collection of merchants
     */
    public function findByProfileId(string $profileId);

    /**
     * Update merchant by ID
     *
     * @param string $merchantId The merchant's UUID
     * @param array $data Merchant data to update
     * @return bool True if update was successful, false otherwise
     */
    public function updateById(string $merchantId, array $data): bool;

    /**
     * Delete merchant by ID
     *
     * @param string $merchantId The merchant's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteById(string $merchantId): bool;

    /**
     * Count merchants by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return int Number of merchants for this profile
     */
    public function countByProfileId(string $profileId): int;

    /**
     * Check if profile can create more merchants
     *
     * @param string $profileId The profile's UUID
     * @param int $maxMerchants Maximum allowed merchants
     * @return bool True if can create more, false otherwise
     */
    public function canCreateMoreMerchants(string $profileId, int $maxMerchants): bool;
}
