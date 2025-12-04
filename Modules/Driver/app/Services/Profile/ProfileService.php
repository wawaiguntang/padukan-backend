<?php

namespace Modules\Driver\Services\Profile;

use Modules\Driver\Enums\GenderEnum;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Models\Profile;
use Modules\Driver\Repositories\Profile\IProfileRepository;
use Modules\Driver\Repositories\Document\IDocumentRepository;
use Modules\Driver\Services\FileUpload\IFileUploadService;
use Modules\Driver\Services\Document\IDocumentService;
use Modules\Driver\Exceptions\ProfileNotFoundException;
use Modules\Driver\Exceptions\ProfileAlreadyExistsException;
use Modules\Driver\Exceptions\FileUploadException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Profile Service Implementation
 *
 * This class handles profile business logic operations
 * for the driver module.
 */
class ProfileService implements IProfileService
{
    /**
     * The profile repository instance
     *
     * @var IProfileRepository
     */
    protected IProfileRepository $profileRepository;

    /**
     * The file upload service instance
     *
     * @var IFileUploadService
     */
    protected IFileUploadService $fileUploadService;

    /**
     * The document repository instance
     *
     * @var IDocumentRepository
     */
    protected IDocumentRepository $documentRepository;

    /**
     * The document service instance
     *
     * @var IDocumentService
     */
    protected IDocumentService $documentService;


    /**
     * Constructor
     *
     * @param IProfileRepository $profileRepository The profile repository instance
     * @param IDocumentRepository $documentRepository The document repository instance
     * @param IDocumentService $documentService The document service instance
     * @param IFileUploadService $fileUploadService The file upload service instance
     */
    public function __construct(
        IProfileRepository $profileRepository,
        IDocumentRepository $documentRepository,
        IDocumentService $documentService,
        IFileUploadService $fileUploadService
    ) {
        $this->profileRepository = $profileRepository;
        $this->documentRepository = $documentRepository;
        $this->documentService = $documentService;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * {@inheritDoc}
     */
    public function createProfile(string $userId, array $data): Profile
    {
        // Check if profile already exists
        if ($this->profileRepository->existsByUserId($userId)) {
            throw new ProfileAlreadyExistsException();
        }

        // Prepare data with user_id
        $profileData = array_merge($data, ['user_id' => $userId]);

        return $this->profileRepository->create($profileData);
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileByUserId(string $userId): ?Profile
    {
        return $this->profileRepository->findByUserId($userId);
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileById(string $profileId): ?Profile
    {
        return $this->profileRepository->findById($profileId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateProfile(string $userId, array $data): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        if (isset($data['avatar_file']) && $data['avatar_file'] instanceof \Illuminate\Http\UploadedFile) {
            try {
                if ($profile->avatar) {
                    $this->fileUploadService->deleteAvatar($profile->avatar);
                }

                $uploadResult = $this->fileUploadService->uploadAvatar($data['avatar_file'], $userId);
                $data['avatar'] = $uploadResult['path'];
            } catch (\Exception $e) {
                Log::error('Avatar upload failed', [
                    'error' => $e->getMessage(),
                    'user_id' => $userId
                ]);
                unset($data['avatar']);
            }
            unset($data['avatar_file']);
        }

        return $this->profileRepository->update($profile->id, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteProfile(string $userId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $this->profileRepository->delete($profile->id);
    }

    /**
     * {@inheritDoc}
     */
    public function updateGender(string $userId, GenderEnum $gender): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $this->profileRepository->updateGender($profile->id, $gender);
    }

    /**
     * {@inheritDoc}
     */
    public function hasProfile(string $userId): bool
    {
        return $this->profileRepository->existsByUserId($userId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateVerificationStatus(string $userId, bool $isVerified, ?string $verificationStatus = null): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $this->profileRepository->updateVerificationStatus($profile->id, $isVerified, $verificationStatus);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAvatar(string $userId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile || !$profile->avatar) {
            return false;
        }

        // Delete the file
        $fileDeleted = $this->fileUploadService->deleteAvatar($profile->avatar);

        if ($fileDeleted) {
            // Update profile to remove avatar reference
            $this->profileRepository->update($profile->id, ['avatar' => null]);
        }

        return $fileDeleted;
    }

    /**
     * {@inheritDoc}
     */
    public function getVerificationStatus(string $userId): ?array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return null;
        }

        // Get ID card document
        $idCardDocument = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::ID_CARD)->first();

        // Get Selfie document
        $selfieDocument = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::SELFIE_WITH_ID_CARD)->first();
        return [
            'profile_verified' => $profile->is_verified,
            'verification_status' => $profile->verification_status,
            'submitted_at' => $profile->updated_at,
            'verified_at' => $profile->verified_at,
            'id_card_document' => $idCardDocument ? [
                'id' => $idCardDocument->id,
                'status' => $idCardDocument->verification_status,
                'submitted_at' => $idCardDocument->created_at,
                'verified_at' => $idCardDocument->verified_at,
                'temporary_url' => $this->fileUploadService->generateTemporaryUrl($idCardDocument->file_path),
            ] : null,
            'selfie_document' => $selfieDocument ? [
                'id' => $selfieDocument->id,
                'status' => $selfieDocument->verification_status,
                'submitted_at' => $selfieDocument->created_at,
                'verified_at' => $selfieDocument->verified_at,
                'temporary_url' => $this->fileUploadService->generateTemporaryUrl($selfieDocument->file_path),
            ] : null,
            'can_resubmit' => $profile->verification_status === \Modules\Driver\Enums\VerificationStatusEnum::REJECTED,
            'can_submit' => $profile->verification_status === \Modules\Driver\Enums\VerificationStatusEnum::PENDING,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function resubmitVerification(string $userId, array $data): ?array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return null;
        }
        try {
            $uploadedDocuments = [];

            $existingIdCard = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::ID_CARD)->first();
            if ($existingIdCard) {
                $this->documentService->deleteDocument($existingIdCard->id);
            }

            $idCardDocument = $this->documentService->uploadDocument(
                $userId,
                DocumentTypeEnum::ID_CARD,
                $data['id_card_file'],
                [
                    'meta' => $data['id_card_meta'],
                    'expiry_date' => $data['id_card_expiry_date'] ?? null,
                ]
            );
            $uploadedDocuments[] = $idCardDocument;

            $existingSelfie = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::SELFIE_WITH_ID_CARD)->first();
            if ($existingSelfie) {
                $this->documentService->deleteDocument($existingSelfie->id);
            }

            $selfieDocument = $this->documentService->uploadDocument(
                $userId,
                DocumentTypeEnum::SELFIE_WITH_ID_CARD,
                $data['selfie_with_id_card_file'],
                [
                    'meta' => $data['selfie_with_id_card_meta'] ?? ['description' => 'Selfie with ID card'],
                ]
            );
            $uploadedDocuments[] = $selfieDocument;

            $this->profileRepository->update($profile->id, [
                'verification_status' => 'on_review',
                'is_verified' => false,
                'verified_at' => null,
            ]);

            $profile = $this->profileRepository->findByUserId($userId);

            $this->documentService->updateVerificationStatus(
                $idCardDocument->id,
                \Modules\Driver\Enums\VerificationStatusEnum::ON_REVIEW
            );

            $this->documentService->updateVerificationStatus(
                $selfieDocument->id,
                \Modules\Driver\Enums\VerificationStatusEnum::ON_REVIEW
            );

            return [
                'verification_id' => $profile->id,
                'documents' => array_map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => $doc->type,
                        'file_name' => basename($doc->file_path),
                        'uploaded_at' => $doc->created_at,
                        'temporary_url' => $this->fileUploadService->generateTemporaryUrl($doc->file_path),
                    ];
                }, $uploadedDocuments),
                'status' => $profile->verification_status->value,
                'resubmitted_at' => now(),
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
