<?php

namespace Modules\Profile\Services\Merchant;

/**
 * Merchant Service Interface
 *
 * Defines methods for merchant business logic
 */
interface IMerchantService
{
    /**
     * Get merchant profile by user ID
     *
     * @param string $userId
     * @return array
     */
    public function getMerchantProfile(string $userId): array;

    /**
     * Create merchant profile
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function createMerchantProfile(string $userId, array $data): array;

    /**
     * Update merchant profile
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function updateMerchantProfile(string $userId, array $data): array;

    /**
     * Request merchant verification
     *
     * @param string $userId
     * @return array
     */
    public function requestMerchantVerification(string $userId): array;

    /**
     * Get merchant verification status
     *
     * @param string $userId
     * @return array
     */
    public function getMerchantVerificationStatus(string $userId): array;

    /**
     * Get merchant documents for verification status check
     *
     * @param string $userId
     * @return array
     */
    public function getMerchantDocumentsForVerification(string $userId): array;

    /**
     * Create merchant document
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function createMerchantDocument(string $userId, array $data): array;

    /**
     * Get merchant document file URL
     *
     * @param string $userId
     * @param string $documentId
     * @return string
     */
    public function getMerchantDocumentFileUrl(string $userId, string $documentId): string;

    /**
     * Create merchant bank account
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function createMerchantBank(string $userId, array $data): array;

    /**
     * Get merchant bank accounts
     *
     * @param string $userId
     * @return array
     */
    public function getMerchantBanks(string $userId): array;

    /**
     * Update merchant bank account
     *
     * @param string $userId
     * @param string $bankId
     * @param array $data
     * @return array
     */
    public function updateMerchantBank(string $userId, string $bankId, array $data): array;

    /**
     * Delete merchant bank account
     *
     * @param string $userId
     * @param string $bankId
     * @return bool
     */
    public function deleteMerchantBank(string $userId, string $bankId): bool;

    /**
     * Create/update merchant business address
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function createMerchantAddress(string $userId, array $data): array;

    /**
     * Get merchant business address
     *
     * @param string $userId
     * @return array
     */
    public function getMerchantAddress(string $userId): array;

    /**
     * Check if user can access merchant document
     *
     * @param string $userId
     * @param string $documentId
     * @return bool
     */
    public function canAccessMerchantDocument(string $userId, string $documentId): bool;
}