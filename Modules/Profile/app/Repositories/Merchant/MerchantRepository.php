<?php

namespace Modules\Profile\Repositories\Merchant;

use Illuminate\Support\Facades\Cache;
use Modules\Profile\Models\MerchantProfile;
use Modules\Profile\Models\MerchantBank;
use Modules\Profile\Models\MerchantAddress;
use Modules\Profile\Models\MerchantDocument;
use Modules\Profile\Cache\KeyManager\KeyManager;

/**
 * Merchant Repository Implementation
 *
 * Handles merchant data access operations with caching
 */
class MerchantRepository implements IMerchantRepository
{
    /**
     * Find merchant profile by profile ID
     */
    public function findMerchantProfileByProfileId(string $profileId): ?MerchantProfile
    {
        $cacheKey = KeyManager::merchantProfileByProfileId($profileId);

        return Cache::remember($cacheKey, 900, function () use ($profileId) { // 15 minutes
            return MerchantProfile::where('profile_id', $profileId)->first();
        });
    }

    /**
     * Create merchant profile
     */
    public function createMerchantProfile(array $data): MerchantProfile
    {
        $profile = MerchantProfile::create($data);

        // Invalidate cache
        Cache::forget(KeyManager::merchantProfileByProfileId($data['profile_id']));

        return $profile;
    }

    /**
     * Update merchant profile
     */
    public function updateMerchantProfile(string $merchantProfileId, array $data): MerchantProfile
    {
        $profile = MerchantProfile::findOrFail($merchantProfileId);
        $profile->update($data);
        $updatedProfile = $profile->fresh();

        // Invalidate cache
        Cache::forget(KeyManager::merchantProfileByProfileId($updatedProfile->profile_id));

        return $updatedProfile;
    }

    /**
     * Create merchant bank account
     */
    public function createBank(string $merchantProfileId, array $data): MerchantBank
    {
        $data['merchant_profile_id'] = $merchantProfileId;
        return MerchantBank::create($data);
    }

    /**
     * Find bank by ID and merchant profile ID
     */
    public function findBankByIdAndMerchantProfileId(string $bankId, string $merchantProfileId): ?MerchantBank
    {
        return MerchantBank::where('id', $bankId)
            ->where('merchant_profile_id', $merchantProfileId)
            ->first();
    }

    /**
     * Update merchant bank
     */
    public function updateBank(string $bankId, array $data, string $merchantProfileId): MerchantBank
    {
        $bank = $this->findBankByIdAndMerchantProfileId($bankId, $merchantProfileId);

        if (!$bank) {
            throw new \Exception('Bank account not found');
        }

        $bank->update($data);
        return $bank->fresh();
    }

    /**
     * Delete merchant bank
     */
    public function deleteBank(string $bankId, string $merchantProfileId): bool
    {
        $bank = $this->findBankByIdAndMerchantProfileId($bankId, $merchantProfileId);

        if (!$bank) {
            return false;
        }

        return $bank->delete();
    }

    /**
     * Get banks by merchant profile ID
     */
    public function getBanksByMerchantProfileId(string $merchantProfileId)
    {
        return MerchantBank::where('merchant_profile_id', $merchantProfileId)
            ->with('bank')
            ->get();
    }

    /**
     * Create merchant address
     */
    public function createAddress(string $merchantProfileId, array $data): MerchantAddress
    {
        $data['merchant_profile_id'] = $merchantProfileId;
        return MerchantAddress::create($data);
    }

    /**
     * Update merchant address
     */
    public function updateAddress(string $merchantProfileId, array $data): MerchantAddress
    {
        $address = $this->findAddressByMerchantProfileId($merchantProfileId);

        if (!$address) {
            return $this->createAddress($merchantProfileId, $data);
        }

        $address->update($data);
        return $address->fresh();
    }

    /**
     * Find address by merchant profile ID
     */
    public function findAddressByMerchantProfileId(string $merchantProfileId): ?MerchantAddress
    {
        return MerchantAddress::where('merchant_profile_id', $merchantProfileId)->first();
    }

    /**
     * Create merchant document
     */
    public function createDocument(string $merchantProfileId, array $data): MerchantDocument
    {
        $data['merchant_profile_id'] = $merchantProfileId;
        return MerchantDocument::create($data);
    }

    /**
     * Find document by ID and merchant profile ID
     */
    public function findDocumentByIdAndMerchantProfileId(string $documentId, string $merchantProfileId): ?MerchantDocument
    {
        return MerchantDocument::where('id', $documentId)
            ->where('merchant_profile_id', $merchantProfileId)
            ->first();
    }

    /**
     * Update merchant document
     */
    public function updateDocument(string $documentId, array $data, string $merchantProfileId): MerchantDocument
    {
        $document = $this->findDocumentByIdAndMerchantProfileId($documentId, $merchantProfileId);

        if (!$document) {
            throw new \Exception('Document not found');
        }

        $document->update($data);
        return $document->fresh();
    }

    /**
     * Delete merchant document
     */
    public function deleteDocument(string $documentId, string $merchantProfileId): bool
    {
        $document = $this->findDocumentByIdAndMerchantProfileId($documentId, $merchantProfileId);

        if (!$document) {
            return false;
        }

        return $document->delete();
    }

    /**
     * Get documents by merchant profile ID
     */
    public function getDocumentsByMerchantProfileId(string $merchantProfileId)
    {
        return MerchantDocument::where('merchant_profile_id', $merchantProfileId)->get();
    }

    /**
     * Get document file URL
     */
    public function getDocumentFileUrl(string $documentId, string $merchantProfileId): string
    {
        $document = $this->findDocumentByIdAndMerchantProfileId($documentId, $merchantProfileId);

        if (!$document) {
            throw new \Exception('Document not found');
        }

        // Return the file path - the service layer will handle URL generation
        return $document->file_path;
    }
}