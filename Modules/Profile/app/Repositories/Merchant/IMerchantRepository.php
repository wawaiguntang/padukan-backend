<?php

namespace Modules\Profile\Repositories\Merchant;

use Modules\Profile\Models\MerchantProfile;
use Modules\Profile\Models\MerchantBank;
use Modules\Profile\Models\MerchantAddress;
use Modules\Profile\Models\MerchantDocument;

/**
 * Merchant Repository Interface
 *
 * Defines methods for merchant data access
 */
interface IMerchantRepository
{
    /**
     * Find merchant profile by profile ID
     *
     * @param string $profileId
     * @return MerchantProfile|null
     */
    public function findMerchantProfileByProfileId(string $profileId): ?MerchantProfile;

    /**
     * Create merchant profile
     *
     * @param array $data
     * @return MerchantProfile
     */
    public function createMerchantProfile(array $data): MerchantProfile;

    /**
     * Update merchant profile
     *
     * @param string $merchantProfileId
     * @param array $data
     * @return MerchantProfile
     */
    public function updateMerchantProfile(string $merchantProfileId, array $data): MerchantProfile;

    /**
     * Create merchant bank account
     *
     * @param string $merchantProfileId
     * @param array $data
     * @return MerchantBank
     */
    public function createBank(string $merchantProfileId, array $data): MerchantBank;

    /**
     * Find bank by ID and merchant profile ID
     *
     * @param string $bankId
     * @param string $merchantProfileId
     * @return MerchantBank|null
     */
    public function findBankByIdAndMerchantProfileId(string $bankId, string $merchantProfileId): ?MerchantBank;

    /**
     * Update merchant bank
     *
     * @param string $bankId
     * @param array $data
     * @param string $merchantProfileId
     * @return MerchantBank
     */
    public function updateBank(string $bankId, array $data, string $merchantProfileId): MerchantBank;

    /**
     * Delete merchant bank
     *
     * @param string $bankId
     * @param string $merchantProfileId
     * @return bool
     */
    public function deleteBank(string $bankId, string $merchantProfileId): bool;

    /**
     * Get banks by merchant profile ID
     *
     * @param string $merchantProfileId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBanksByMerchantProfileId(string $merchantProfileId);

    /**
     * Create merchant address
     *
     * @param string $merchantProfileId
     * @param array $data
     * @return MerchantAddress
     */
    public function createAddress(string $merchantProfileId, array $data): MerchantAddress;

    /**
     * Update merchant address
     *
     * @param string $merchantProfileId
     * @param array $data
     * @return MerchantAddress
     */
    public function updateAddress(string $merchantProfileId, array $data): MerchantAddress;

    /**
     * Find address by merchant profile ID
     *
     * @param string $merchantProfileId
     * @return MerchantAddress|null
     */
    public function findAddressByMerchantProfileId(string $merchantProfileId): ?MerchantAddress;

    /**
     * Create merchant document
     *
     * @param string $merchantProfileId
     * @param array $data
     * @return MerchantDocument
     */
    public function createDocument(string $merchantProfileId, array $data): MerchantDocument;

    /**
     * Find document by ID and merchant profile ID
     *
     * @param string $documentId
     * @param string $merchantProfileId
     * @return MerchantDocument|null
     */
    public function findDocumentByIdAndMerchantProfileId(string $documentId, string $merchantProfileId): ?MerchantDocument;

    /**
     * Update merchant document
     *
     * @param string $documentId
     * @param array $data
     * @param string $merchantProfileId
     * @return MerchantDocument
     */
    public function updateDocument(string $documentId, array $data, string $merchantProfileId): MerchantDocument;

    /**
     * Delete merchant document
     *
     * @param string $documentId
     * @param string $merchantProfileId
     * @return bool
     */
    public function deleteDocument(string $documentId, string $merchantProfileId): bool;

    /**
     * Get documents by merchant profile ID
     *
     * @param string $merchantProfileId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDocumentsByMerchantProfileId(string $merchantProfileId);

    /**
     * Get document file URL
     *
     * @param string $documentId
     * @param string $merchantProfileId
     * @return string
     */
    public function getDocumentFileUrl(string $documentId, string $merchantProfileId): string;
}