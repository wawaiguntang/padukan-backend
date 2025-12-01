<?php

namespace Modules\Driver\Services\Document;

use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Enums\VerificationStatusEnum;
use Modules\Driver\Models\Document;
use Modules\Driver\Repositories\Document\IDocumentRepository;
use Modules\Driver\Repositories\Profile\IProfileRepository;
use Modules\Driver\Services\FileUpload\IFileUploadService;
use Modules\Driver\Exceptions\ProfileNotFoundException;
use Modules\Driver\Exceptions\DocumentNotFoundException;
use Modules\Driver\Exceptions\FileUploadException;
use Illuminate\Http\UploadedFile;

/**
 * Document Service Implementation
 *
 * This class handles document business logic operations
 * for the driver module.
 */
class DocumentService implements IDocumentService
{
    /**
     * The document repository instance
     *
     * @var IDocumentRepository
     */
    protected IDocumentRepository $documentRepository;

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
     * Constructor
     *
     * @param IDocumentRepository $documentRepository The document repository instance
     * @param IProfileRepository $profileRepository The profile repository instance
     * @param IFileUploadService $fileUploadService The file upload service instance
     */
    public function __construct(
        IDocumentRepository $documentRepository,
        IProfileRepository $profileRepository,
        IFileUploadService $fileUploadService
    ) {
        $this->documentRepository = $documentRepository;
        $this->profileRepository = $profileRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * {@inheritDoc}
     */
    public function uploadDocument(string $userId, DocumentTypeEnum $documentType, UploadedFile $documentFile, array $additionalData = []): Document
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException();
        }

        try {
            $uploadResult = $this->fileUploadService->uploadDocument($documentFile, $userId, $documentType->value);

            $documentData = array_merge($additionalData, [
                'documentable_id' => $profile->id,
                'documentable_type' => \Modules\Driver\Models\Profile::class,
                'type' => $documentType,
                'file_path' => $uploadResult['path'],
                'file_name' => $uploadResult['filename'],
                'mime_type' => $uploadResult['mime_type'],
                'file_size' => $uploadResult['size'],
            ]);

            return $this->documentRepository->create($documentData);
        } catch (\Exception $e) {
            throw new FileUploadException('file.upload_failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentsByUserId(string $userId): Collection
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException();
        }

        return $this->documentRepository->findByProfileId($profile->id);
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentById(string $documentId): ?Document
    {
        return $this->documentRepository->findById($documentId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateVerificationStatus(string $documentId, VerificationStatusEnum $status, ?string $verifiedBy = null): bool
    {
        return $this->documentRepository->updateVerificationStatus($documentId, $status, $verifiedBy);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDocument(string $documentId): bool
    {
        $document = $this->documentRepository->findById($documentId);

        if (!$document) {
            return false;
        }

        // Check if user can delete this document (we'll need to get userId from somewhere)
        // For now, we'll assume the caller has already checked permissions
        // In a real implementation, this method should receive userId as parameter

        // Delete the file first
        $fileDeleted = $this->fileUploadService->deleteDocument($document->file_path);

        // If file deletion failed, we still proceed with database deletion
        // to avoid orphaned records, but log the issue
        if (!$fileDeleted) {
            // You might want to log this: "Failed to delete document file: {$document->file_path}"
        }

        return $this->documentRepository->delete($documentId);
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentsByType(string $userId, DocumentTypeEnum $documentType): Collection
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException();
        }

        return $this->documentRepository->findByTypeAndProfileId($profile->id, $documentType);
    }

    /**
     * {@inheritDoc}
     */
    public function createDocument(string $userId, DocumentTypeEnum $documentType, string $filePath, array $additionalData = []): Document
    {
        // Find user profile
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException();
        }

        // Prepare document data
        $documentData = array_merge($additionalData, [
            'documentable_id' => $profile->id,
            'documentable_type' => \Modules\Driver\Models\Profile::class,
            'type' => $documentType,
            'file_path' => $filePath,
        ]);

        return $this->documentRepository->create($documentData);
    }
}
