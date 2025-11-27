<?php

namespace Modules\Profile\Policies;

use Modules\Profile\Models\CustomerDocument;
use Modules\Profile\Models\DriverDocument;
use Modules\Profile\Models\MerchantDocument;
use Modules\Profile\Repositories\Profile\IProfileRepository;

/**
 * Document Ownership Policy
 *
 * Ensures users can only access their own documents
 */
class DocumentOwnershipPolicy
{
    private IProfileRepository $profileRepository;

    public function __construct(IProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Check if user can access the document
     *
     * @param string $userId
     * @param string $documentId
     * @return bool
     */
    public function canAccessDocument(string $userId, string $documentId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        $customerProfile = $profile->customerProfile;

        if (!$customerProfile) {
            return false;
        }

        $document = CustomerDocument::find($documentId);

        if (!$document) {
            return false;
        }

        // Check if document belongs to the user's customer profile
        return $document->customer_profile_id === $customerProfile->id;
    }

    /**
     * Check if user owns the document
     *
     * @param string $userId
     * @param CustomerDocument $document
     * @return bool
     */
    public function ownsDocument(string $userId, CustomerDocument $document): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        $customerProfile = $profile->customerProfile;

        if (!$customerProfile) {
            return false;
        }

        return $document->customer_profile_id === $customerProfile->id;
    }

    /**
     * Check if user can access driver document
     *
     * @param string $userId
     * @param string $documentId
     * @return bool
     */
    public function canAccessDriverDocument(string $userId, string $documentId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            return false;
        }

        $document = DriverDocument::find($documentId);

        if (!$document) {
            return false;
        }

        // Check if document belongs to the user's driver profile
        return $document->driver_profile_id === $driverProfile->id;
    }

    /**
     * Check if user owns the driver document
     *
     * @param string $userId
     * @param DriverDocument $document
     * @return bool
     */
    public function ownsDriverDocument(string $userId, DriverDocument $document): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            return false;
        }

        return $document->driver_profile_id === $driverProfile->id;
    }

    /**
     * Check if user can access merchant document
     *
     * @param string $userId
     * @param string $documentId
     * @return bool
     */
    public function canAccessMerchantDocument(string $userId, string $documentId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            return false;
        }

        $document = MerchantDocument::find($documentId);

        if (!$document) {
            return false;
        }

        // Check if document belongs to the user's merchant profile
        return $document->merchant_profile_id === $merchantProfile->id;
    }

    /**
     * Check if user owns the merchant document
     *
     * @param string $userId
     * @param MerchantDocument $document
     * @return bool
     */
    public function ownsMerchantDocument(string $userId, MerchantDocument $document): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            return false;
        }

        return $document->merchant_profile_id === $merchantProfile->id;
    }
}