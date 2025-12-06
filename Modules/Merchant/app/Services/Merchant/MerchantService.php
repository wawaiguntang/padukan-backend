<?php

namespace Modules\Merchant\Services\Merchant;

use Illuminate\Support\Facades\DB;
use Modules\Merchant\Models\Merchant;
use Modules\Merchant\Repositories\Merchant\IMerchantRepository;
use Modules\Merchant\Repositories\Profile\IProfileRepository;
use Modules\Merchant\Services\Setting\IMerchantSettingService;
use Modules\Merchant\Services\Document\IDocumentService;
use Modules\Merchant\Services\FileUpload\IFileUploadService;
use Modules\Merchant\Enums\DocumentTypeEnum;
use Modules\Merchant\Enums\VerificationStatusEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Merchant Service Implementation
 *
 * Handles merchant business logic operations
 */
class MerchantService implements IMerchantService
{
    private IMerchantRepository $merchantRepository;
    private IProfileRepository $profileRepository;
    private IMerchantSettingService $merchantSettingService;
    private IDocumentService $documentService;
    private IFileUploadService $fileUploadService;

    public function __construct(
        IMerchantRepository $merchantRepository,
        IProfileRepository $profileRepository,
        IMerchantSettingService $merchantSettingService,
        IDocumentService $documentService,
        IFileUploadService $fileUploadService
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->profileRepository = $profileRepository;
        $this->merchantSettingService = $merchantSettingService;
        $this->documentService = $documentService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create a new merchant with default settings
     */
    public function createMerchant(string $profileId, array $data): Merchant
    {
        return DB::connection('merchant')->transaction(function () use ($profileId, $data) {
            $data['profile_id'] = $profileId;

            // Generate slug from business name if not provided
            if (!isset($data['slug']) && isset($data['business_name'])) {
                $data['slug'] = $this->generateSlug($data['business_name']);
            }

            $data['regular_hours'] = json_encode([
                'monday' => ['open' => '09:00', 'close' => '17:00'],
                'tuesday' => ['open' => '09:00', 'close' => '17:00'],
                'wednesday' => ['open' => '09:00', 'close' => '17:00'],
                'thursday' => ['open' => '09:00', 'close' => '17:00'],
                'friday' => ['open' => '09:00', 'close' => '17:00'],
                'saturday' => ['open' => '10:00', 'close' => '15:00'],
                'sunday' => ['open' => 'Closed', 'close' => 'Closed'],
            ]);

            // Handle logo upload
            if (isset($data['logo_file']) && $data['logo_file'] instanceof UploadedFile) {
                try {
                    $uploadResult = $this->fileUploadService->uploadAvatar($data['logo_file'], $profileId);
                    $data['logo'] = $uploadResult['path'];
                } catch (\Exception $e) {
                    Log::error('Merchant logo upload failed', [
                        'error' => $e->getMessage(),
                        'profile_id' => $profileId
                    ]);
                    unset($data['logo']);
                }
                unset($data['logo_file']);
            }

            // Create merchant
            $merchant = $this->merchantRepository->create($data);

            // Create default settings for the merchant
            $this->merchantSettingService->createDefaultSettings($merchant->id);

            return $merchant;
        });
    }

    /**
     * Get merchant by ID
     */
    public function getMerchantById(string $merchantId): ?Merchant
    {
        return $this->merchantRepository->findById($merchantId);
    }

    /**
     * Get merchant by ID with settings
     */
    public function getMerchantWithSettings(string $merchantId): ?Merchant
    {
        $merchant = $this->merchantRepository->findById($merchantId);

        if ($merchant) {
            $merchant->load(['settings']);
        }

        return $merchant;
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
        $merchant = $this->merchantRepository->findById($merchantId);

        if (!$merchant) {
            return false;
        }

        // Handle logo upload
        if (isset($data['logo_file']) && $data['logo_file'] instanceof UploadedFile) {
            try {
                if ($merchant->logo) {
                    $this->fileUploadService->deleteAvatar($merchant->logo);
                }

                $uploadResult = $this->fileUploadService->uploadAvatar($data['logo_file'], $merchant->profile_id);
                $data['logo'] = $uploadResult['path'];
            } catch (\Exception $e) {
                Log::error('Merchant logo upload failed', [
                    'error' => $e->getMessage(),
                    'merchant_id' => $merchantId
                ]);
                unset($data['logo']);
            }
            unset($data['logo_file']);
        }

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
     * Resubmit verification for a merchant with documents and logo
     */
    public function resubmitVerification(string $merchantId, array $data): ?array
    {
        $merchant = $this->merchantRepository->findById($merchantId);

        if (!$merchant) {
            return null;
        }

        try {
            $uploadedDocuments = [];

            // Delete existing merchant documents
            $existingMerchantDocs = $this->documentService->getDocumentsByMerchantIdAndType($merchantId, DocumentTypeEnum::MERCHANT);
            foreach ($existingMerchantDocs as $doc) {
                $this->documentService->deleteDocument($doc->id);
            }

            // Delete existing banner documents
            $existingBannerDocs = $this->documentService->getDocumentsByMerchantIdAndType($merchantId, DocumentTypeEnum::BANNER);
            foreach ($existingBannerDocs as $doc) {
                $this->documentService->deleteDocument($doc->id);
            }

            // Upload merchant document
            $merchantDocument = $this->documentService->uploadMerchantDocument(
                $merchantId,
                DocumentTypeEnum::MERCHANT,
                $data['merchant_document_file'],
                [
                    'meta' => $data['merchant_document_meta'] ?? ['description' => 'Merchant Document'],
                ]
            );
            $uploadedDocuments[] = $merchantDocument;

            // Upload banner document
            $bannerDocument = $this->documentService->uploadMerchantDocument(
                $merchantId,
                DocumentTypeEnum::BANNER,
                $data['banner_file'],
                [
                    'meta' => $data['banner_meta'] ?? ['description' => 'Banner Image'],
                ]
            );
            $uploadedDocuments[] = $bannerDocument;

            // Update merchant verification status
            $this->merchantRepository->updateById($merchantId, [
                'verification_status' => VerificationStatusEnum::ON_REVIEW->value,
                'is_verified' => false,
                'verified_at' => null,
            ]);

            $merchant = $this->merchantRepository->findById($merchantId);

            // Update document verification statuses
            $this->documentService->updateVerificationStatus(
                $merchantDocument->id,
                VerificationStatusEnum::ON_REVIEW
            );

            $this->documentService->updateVerificationStatus(
                $bannerDocument->id,
                VerificationStatusEnum::ON_REVIEW
            );

            return [
                'merchant_id' => $merchant->id,
                'documents' => array_map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => $doc->type,
                        'file_name' => basename($doc->file_path),
                        'uploaded_at' => $doc->created_at,
                        'temporary_url' => $this->fileUploadService->generateTemporaryUrl($doc->file_path),
                    ];
                }, $uploadedDocuments),
                'status' => $merchant->verification_status,
                'resubmitted_at' => now(),
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete merchant logo
     */
    public function deleteLogo(string $merchantId): bool
    {
        $merchant = $this->merchantRepository->findById($merchantId);

        if (!$merchant || !$merchant->logo) {
            return false;
        }

        // Delete the file
        $fileDeleted = $this->fileUploadService->deleteAvatar($merchant->logo);

        if ($fileDeleted) {
            // Update merchant to remove logo reference
            $this->merchantRepository->updateById($merchantId, ['logo' => null]);
        }

        return $fileDeleted;
    }

    /**
     * Get merchant address
     */
    public function getMerchantAddress(string $merchantId)
    {
        $merchant = $this->merchantRepository->findById($merchantId);

        if (!$merchant) {
            return null;
        }

        return [
            'street' => $merchant->street,
            'city' => $merchant->city,
            'province' => $merchant->province,
            'country' => $merchant->country,
            'postal_code' => $merchant->postal_code,
            'latitude' => $merchant->latitude,
            'longitude' => $merchant->longitude,
        ];
    }

    /**
     * Get merchant documents
     */
    public function getMerchantDocuments(string $merchantId)
    {
        // Validate merchant ID format
        if (empty($merchantId) || !\Illuminate\Support\Str::isUuid($merchantId)) {
            return collect();
        }

        $merchant = $this->merchantRepository->findById($merchantId);

        if (!$merchant) {
            return collect();
        }

        return $this->documentService->getDocumentsByMerchantId($merchantId);
    }

    /**
     * Get merchant schedule
     */
    public function getMerchantSchedule(string $merchantId)
    {
        $merchant = $this->merchantRepository->findById($merchantId);

        if (!$merchant) {
            return null;
        }

        return [
            'regular_hours' => $merchant->regular_hours ? json_decode($merchant->regular_hours, true) : null,
            'special_schedules' => $merchant->special_schedules ? json_decode($merchant->special_schedules, true) : null,
        ];
    }

    /**
     * Update merchant address
     */
    public function updateMerchantAddress(string $merchantId, array $data)
    {
        return $this->updateMerchant($merchantId, $data);
    }

    /**
     * Update merchant schedule
     */
    public function updateSchedule(string $merchantId, array $data): bool
    {
        // Encode schedule data as JSON
        if (isset($data['regular_hours'])) {
            $data['regular_hours'] = json_encode($data['regular_hours']);
        }
        if (isset($data['special_schedules'])) {
            $data['special_schedules'] = json_encode($data['special_schedules']);
        }

        return $this->merchantRepository->updateById($merchantId, $data);
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
