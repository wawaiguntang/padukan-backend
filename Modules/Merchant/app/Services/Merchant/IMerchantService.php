<?php

namespace Modules\Merchant\Services\Merchant;

use Modules\Merchant\Models\Merchant;

/**
 * Interface for Merchant Service
 *
 * This interface defines the contract for merchant business logic operations
 * in the merchant module.
 */
interface IMerchantService
{
    /**
     * Create a new merchant
     *
     * @param string $profileId The profile's UUID
     * @param array $data Merchant data
     * @return Merchant The created merchant model
     */
    public function createMerchant(string $profileId, array $data): Merchant;

    /**
     * Get merchant by ID
     *
     * @param string $merchantId The merchant's UUID
     * @return Merchant|null The merchant model if found, null otherwise
     */
    public function getMerchantById(string $merchantId): ?Merchant;

    /**
     * Get merchant by ID with settings
     *
     * @param string $merchantId The merchant's UUID
     * @return Merchant|null The merchant model with settings loaded if found, null otherwise
     */
    public function getMerchantWithSettings(string $merchantId): ?Merchant;

    /**
     * Get merchants by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return \Illuminate\Database\Eloquent\Collection Collection of merchants
     */
    public function getMerchantsByProfileId(string $profileId);

    /**
     * Update merchant information
     *
     * @param string $merchantId The merchant's UUID
     * @param array $data Merchant data to update
     * @return bool True if update was successful, false otherwise
     */
    public function updateMerchant(string $merchantId, array $data): bool;

    /**
     * Delete a merchant
     *
     * @param string $merchantId The merchant's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteMerchant(string $merchantId): bool;

    /**
     * Check if profile can create more merchants
     *
     * @param string $profileId The profile's UUID
     * @return bool True if can create more, false otherwise
     */
    public function canCreateMerchant(string $profileId): bool;

    /**
     * Update merchant status
     *
     * @param string $merchantId The merchant's UUID
     * @param \Modules\Merchant\Enums\MerchantStatusEnum $status The new status
     * @return bool True if update was successful, false otherwise
     */
    public function updateStatus(string $merchantId, \Modules\Merchant\Enums\MerchantStatusEnum $status): bool;

    /**
     * Update merchant verification status
     *
     * @param string $merchantId The merchant's UUID
     * @param bool $isVerified Whether the merchant is verified
     * @param string|null $verificationStatus The verification status enum value
     * @return bool True if update was successful, false otherwise
     */
    public function updateVerificationStatus(string $merchantId, bool $isVerified, ?string $verificationStatus = null): bool;

    /**
     * Resubmit verification for a merchant with documents and logo
     *
     * @param string $merchantId The merchant's UUID
     * @param array $data Document and logo data
     * @return array|null Verification result data if successful, null if merchant not found
     */
    public function resubmitVerification(string $merchantId, array $data): ?array;

    /**
     * Get merchant address
     *
     * @param string $merchantId The merchant's UUID
     * @return mixed The address model or null
     */
    public function getMerchantAddress(string $merchantId);

    /**
     * Get merchant documents
     *
     * @param string $merchantId The merchant's UUID
     * @return \Illuminate\Database\Eloquent\Collection Collection of documents
     */
    public function getMerchantDocuments(string $merchantId);

    /**
     * Get merchant schedule
     *
     * @param string $merchantId The merchant's UUID
     * @return array|null Schedule data or null if merchant not found
     */
    public function getMerchantSchedule(string $merchantId);

    /**
     * Update merchant address
     *
     * @param string $merchantId The merchant's UUID
     * @param array $data Address data
     * @return mixed The address model
     */
    public function updateMerchantAddress(string $merchantId, array $data);

    /**
     * Update merchant schedule
     *
     * @param string $merchantId The merchant's UUID
     * @param array $data Schedule data
     * @return bool True if update was successful, false otherwise
     */
    public function updateSchedule(string $merchantId, array $data): bool;

    /**
     * Delete merchant logo
     *
     * @param string $merchantId The merchant's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteLogo(string $merchantId): bool;
}
