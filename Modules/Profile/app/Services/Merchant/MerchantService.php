<?php

namespace Modules\Profile\Services\Merchant;

use Modules\Profile\Exceptions\MerchantProfileNotFoundException;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Repositories\Merchant\IMerchantRepository;
use Modules\Profile\Repositories\Profile\IProfileRepository;
use Modules\Profile\Services\FileUpload\IFileUploadService;

/**
 * Merchant Service Implementation
 *
 * Handles merchant business logic
 */
class MerchantService implements IMerchantService
{
    private IMerchantRepository $merchantRepository;
    private IProfileRepository $profileRepository;
    private IFileUploadService $fileUploadService;

    public function __construct(
        IMerchantRepository $merchantRepository,
        IProfileRepository $profileRepository,
        IFileUploadService $fileUploadService
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->profileRepository = $profileRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get merchant profile by user ID
     */
    public function getMerchantProfile(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        return [
            'profile' => $profile,
            'merchant_profile' => $merchantProfile,
            'business_address' => $merchantProfile->address,
            'bank_accounts' => $merchantProfile->banks,
        ];
    }

    /**
     * Create merchant profile
     */
    public function createMerchantProfile(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $this->merchantRepository->createMerchantProfile([
            'profile_id' => $profile->id,
            'business_name' => $data['business_name'],
            'business_type' => $data['business_type'],
            'business_phone' => $data['business_phone'],
            'is_verified' => false,
            'verification_status' => 'pending',
        ]);

        return [
            'profile' => $profile,
            'merchant_profile' => $merchantProfile,
        ];
    }

    /**
     * Update merchant profile
     */
    public function updateMerchantProfile(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        $updatedProfile = $this->merchantRepository->updateMerchantProfile($merchantProfile->id, $data);

        return [
            'profile' => $profile,
            'merchant_profile' => $updatedProfile,
        ];
    }

    /**
     * Request merchant verification
     */
    public function requestMerchantVerification(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        // Check if all required documents are present
        $documents = $merchantProfile->documents;
        $requiredTypes = ['id_card', 'store'];
        $submittedTypes = $documents->pluck('type')->toArray();

        $missingDocuments = array_diff($requiredTypes, $submittedTypes);

        if (!empty($missingDocuments)) {
            throw new \Exception(__('profile::validation.all_documents_required', [
                'types' => implode(', ', $missingDocuments)
            ]));
        }

        // Update verification status
        $updatedProfile = $this->merchantRepository->updateMerchantProfile($merchantProfile->id, [
            'verification_status' => 'pending'
        ]);

        return [
            'profile' => $profile,
            'merchant_profile' => $updatedProfile,
            'verification_requested' => true,
        ];
    }

    /**
     * Get merchant verification status
     */
    public function getMerchantVerificationStatus(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        return [
            'profile' => $profile,
            'merchant_profile' => $merchantProfile,
            'verification_status' => $merchantProfile->verification_status,
            'is_verified' => $merchantProfile->is_verified,
        ];
    }

    /**
     * Get merchant documents for verification status check
     */
    public function getMerchantDocumentsForVerification(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        $documents = $merchantProfile->documents;

        // Check if all required document types are present
        $requiredTypes = ['id_card', 'store'];
        $submittedTypes = $documents->pluck('type')->toArray();

        $missingDocuments = array_diff($requiredTypes, $submittedTypes);

        return [
            'profile' => $profile,
            'merchant_profile' => $merchantProfile,
            'documents' => $documents,
            'required_types' => $requiredTypes,
            'submitted_types' => $submittedTypes,
            'missing_documents' => array_values($missingDocuments),
            'is_complete' => empty($missingDocuments),
        ];
    }

    /**
     * Create merchant document
     */
    public function createMerchantDocument(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        // Upload file
        $uploadedFile = $this->fileUploadService->uploadDocument($data['file'], $userId, 'merchant');

        $document = $this->merchantRepository->createDocument($merchantProfile->id, [
            'type' => $data['type'],
            'file_path' => $uploadedFile['path'],
            'file_name' => $uploadedFile['file_name'],
            'mime_type' => $uploadedFile['mime_type'],
            'file_size' => $uploadedFile['file_size'],
            'meta' => $data['meta'] ?? [],
            'expiry_date' => $data['expiry_date'] ?? null,
            'is_verified' => false,
            'verification_status' => 'pending',
        ]);

        return [
            'document' => $document,
            'uploaded_file' => $uploadedFile,
        ];
    }

    /**
     * Get merchant document file URL
     */
    public function getMerchantDocumentFileUrl(string $userId, string $documentId): string
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        $filePath = $this->merchantRepository->getDocumentFileUrl($documentId, $merchantProfile->id);

        // Generate temporary URL for private file
        return $this->fileUploadService->getFileUrl($filePath);
    }

    /**
     * Create merchant bank account
     */
    public function createMerchantBank(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        $bank = $this->merchantRepository->createBank($merchantProfile->id, [
            'bank_id' => $data['bank_id'],
            'account_number' => $data['account_number'],
            'is_primary' => $data['is_primary'] ?? false,
        ]);

        return [
            'bank' => $bank,
        ];
    }

    /**
     * Get merchant bank accounts
     */
    public function getMerchantBanks(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        $banks = $merchantProfile->banks;

        return [
            'banks' => $banks,
        ];
    }

    /**
     * Update merchant bank account
     */
    public function updateMerchantBank(string $userId, string $bankId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        $bank = $this->merchantRepository->updateBank($bankId, $data, $merchantProfile->id);

        return [
            'bank' => $bank,
        ];
    }

    /**
     * Delete merchant bank account
     */
    public function deleteMerchantBank(string $userId, string $bankId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        return $this->merchantRepository->deleteBank($bankId, $merchantProfile->id);
    }

    /**
     * Create/update merchant business address
     */
    public function createMerchantAddress(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        $address = $this->merchantRepository->createAddress($merchantProfile->id, [
            'street' => $data['street'],
            'city' => $data['city'],
            'province' => $data['province'],
            'postal_code' => $data['postal_code'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        return [
            'address' => $address,
        ];
    }

    /**
     * Get merchant business address
     */
    public function getMerchantAddress(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $merchantProfile = $profile->merchantProfile;

        if (!$merchantProfile) {
            throw new MerchantProfileNotFoundException($userId);
        }

        $address = $merchantProfile->address;

        return [
            'address' => $address,
        ];
    }

    /**
     * Check if user can access merchant document
     */
    public function canAccessMerchantDocument(string $userId, string $documentId): bool
    {
        $policy = new \Modules\Profile\Policies\DocumentOwnershipPolicy($this->profileRepository);
        return $policy->canAccessMerchantDocument($userId, $documentId);
    }
}