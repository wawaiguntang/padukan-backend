<?php

namespace Modules\Profile\Services\Customer;

use Modules\Profile\Repositories\Profile\IProfileRepository;
use Modules\Profile\Repositories\Customer\ICustomerDocumentRepository;
use Modules\Profile\Policies\ProfileOwnership\IProfileOwnershipPolicy;
use Modules\Profile\Policies\DocumentOwnershipPolicy;
use Modules\Profile\Services\FileUpload\IFileUploadService;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\DocumentNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

class CustomerDocumentService implements ICustomerDocumentService
{
    private IProfileRepository $profileRepository;
    private ICustomerDocumentRepository $documentRepository;
    private IProfileOwnershipPolicy $ownershipPolicy;
    private DocumentOwnershipPolicy $documentOwnershipPolicy;
    private IFileUploadService $fileUploadService;

    public function __construct(
        IProfileRepository $profileRepository,
        ICustomerDocumentRepository $documentRepository,
        IProfileOwnershipPolicy $ownershipPolicy,
        DocumentOwnershipPolicy $documentOwnershipPolicy,
        IFileUploadService $fileUploadService
    ) {
        $this->profileRepository = $profileRepository;
        $this->documentRepository = $documentRepository;
        $this->ownershipPolicy = $ownershipPolicy;
        $this->documentOwnershipPolicy = $documentOwnershipPolicy;
        $this->fileUploadService = $fileUploadService;
    }

    public function getDocuments(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $customerProfile = $profile->customerProfile;
        $documents = $this->documentRepository->getByCustomerProfileId($customerProfile->id);

        // Add temporary URLs for private documents
        $documents->transform(function ($document) {
            $document->file_url = $this->fileUploadService->getFileUrl($document->file_path);
            return $document;
        });

        return [
            'profile' => $profile,
            'customer_profile' => $customerProfile,
            'documents' => $documents,
        ];
    }

    public function getDocument(string $userId, string $documentId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $customerProfile = $profile->customerProfile;
        $document = $this->documentRepository->findById($documentId);

        if (!$document || $document->customer_profile_id !== $customerProfile->id) {
            throw new DocumentNotFoundException($documentId);
        }

        // Add temporary URL for private document
        $document->file_url = $this->fileUploadService->getFileUrl($document->file_path);

        return [
            'profile' => $profile,
            'customer_profile' => $customerProfile,
            'document' => $document,
        ];
    }

    public function createDocument(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $customerProfile = $profile->customerProfile;

        // Handle file upload
        if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadData = $this->fileUploadService->uploadDocument($data['file'], $userId, $data['type']);

            $data['file_path'] = $uploadData['path'];
            $data['file_name'] = $uploadData['file_name'];
            $data['mime_type'] = $uploadData['mime_type'];
            $data['file_size'] = $uploadData['file_size'];
        }

        $data['customer_profile_id'] = $customerProfile->id;
        $document = $this->documentRepository->create($data);

        // Add temporary URL
        $document->file_url = $this->fileUploadService->getFileUrl($document->file_path);

        return [
            'profile' => $profile,
            'customer_profile' => $customerProfile,
            'document' => $document,
        ];
    }

    public function updateDocument(string $userId, string $documentId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $customerProfile = $profile->customerProfile;
        $document = $this->documentRepository->findById($documentId);

        if (!$document || $document->customer_profile_id !== $customerProfile->id) {
            throw new DocumentNotFoundException($documentId);
        }

        // Handle file upload if new file provided
        if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old file
            if ($document->file_path) {
                $this->fileUploadService->deleteFile($document->file_path);
            }

            $uploadData = $this->fileUploadService->uploadDocument($data['file'], $userId, $data['type'] ?? $document->type);

            $data['file_path'] = $uploadData['path'];
            $data['file_name'] = $uploadData['file_name'];
            $data['mime_type'] = $uploadData['mime_type'];
            $data['file_size'] = $uploadData['file_size'];
        }

        $success = $this->documentRepository->update($documentId, $data);

        if (!$success) {
            throw new \Exception(__('profile::validation.update_failed'));
        }

        $updatedDocument = $this->documentRepository->findById($documentId);
        $updatedDocument->file_url = $this->fileUploadService->getFileUrl($updatedDocument->file_path);

        return [
            'profile' => $profile,
            'customer_profile' => $customerProfile,
            'document' => $updatedDocument,
        ];
    }

    public function deleteDocument(string $userId, string $documentId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $customerProfile = $profile->customerProfile;
        $document = $this->documentRepository->findById($documentId);

        if (!$document || $document->customer_profile_id !== $customerProfile->id) {
            throw new DocumentNotFoundException($documentId);
        }

        // Delete file from storage
        if ($document->file_path) {
            $this->fileUploadService->deleteFile($document->file_path);
        }

        return $this->documentRepository->delete($documentId);
    }

    public function getDocumentFileUrl(string $userId, string $documentId): string
    {
        // Check document ownership using policy
        if (!$this->documentOwnershipPolicy->canAccessDocument($userId, $documentId)) {
            throw new UnauthorizedAccessException();
        }

        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $customerProfile = $profile->customerProfile;
        $document = $this->documentRepository->findById($documentId);

        if (!$document || $document->customer_profile_id !== $customerProfile->id) {
            throw new DocumentNotFoundException($documentId);
        }

        return $this->fileUploadService->getFileUrl($document->file_path);
    }
}