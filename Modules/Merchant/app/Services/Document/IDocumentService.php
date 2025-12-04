<?php

namespace Modules\Merchant\Services\Document;

use Illuminate\Database\Eloquent\Collection;
use Modules\Merchant\Enums\DocumentTypeEnum;
use Modules\Merchant\Enums\VerificationStatusEnum;
use Modules\Merchant\Models\Document;

/**
 * Interface for Document Service
 *
 * This interface defines the contract for document business logic operations
 * in the merchant module.
 */
interface IDocumentService
{
    /**
     * Upload a document for a user profile
     *
     * @param string $userId The user's UUID
     * @param DocumentTypeEnum $documentType The type of document
     * @param \Illuminate\Http\UploadedFile $documentFile The document file to upload
     * @param array $additionalData Additional document data (optional)
     * @return Document The created document model
     * @throws \Exception If upload fails or profile not found
     */
    public function uploadDocument(string $userId, DocumentTypeEnum $documentType, \Illuminate\Http\UploadedFile $documentFile, array $additionalData = []): Document;

    /**
     * Get documents by user ID
     *
     * @param string $userId The user's UUID
     * @return Collection<Document> Collection of document models
     */
    public function getDocumentsByUserId(string $userId): Collection;

    /**
     * Get document by ID
     *
     * @param string $documentId The document's UUID
     * @return Document|null The document model if found, null otherwise
     */
    public function getDocumentById(string $documentId): ?Document;

    /**
     * Update document verification status
     *
     * @param string $documentId The document's UUID
     * @param VerificationStatusEnum $status The new verification status
     * @param string|null $verifiedBy The user who verified (optional)
     * @return bool True if update was successful, false otherwise
     */
    public function updateVerificationStatus(string $documentId, VerificationStatusEnum $status, ?string $verifiedBy = null): bool;

    /**
     * Delete a document
     *
     * @param string $documentId The document's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function deleteDocument(string $documentId): bool;

    /**
     * Get documents by type for a user
     *
     * @param string $userId The user's UUID
     * @param DocumentTypeEnum $documentType The document type
     * @return Collection<Document> Collection of document models
     */
    public function getDocumentsByType(string $userId, DocumentTypeEnum $documentType): Collection;

    /**
     * Create a document with direct path for a user profile
     *
     * @param string $userId The user's UUID
     * @param DocumentTypeEnum $documentType The type of document
     * @param string $filePath The file path
     * @param array $additionalData Additional document data (optional)
     * @return Document The created document model
     * @throws \Exception If profile not found
     */
    public function createDocument(string $userId, DocumentTypeEnum $documentType, string $filePath, array $additionalData = []): Document;

    /**
     * Upload document for a specific merchant
     *
     * @param string $merchantId The merchant's UUID
     * @param DocumentTypeEnum $documentType The type of document
     * @param \Illuminate\Http\UploadedFile $documentFile The document file to upload
     * @param array $additionalData Additional document data (optional)
     * @return Document The created document model
     * @throws \Exception If upload fails
     */
    public function uploadMerchantDocument(string $merchantId, DocumentTypeEnum $documentType, \Illuminate\Http\UploadedFile $documentFile, array $additionalData = []): Document;

    /**
     * Get documents by merchant ID
     *
     * @param string $merchantId The merchant's UUID
     * @return Collection<Document> Collection of document models
     */
    public function getDocumentsByMerchantId(string $merchantId): Collection;

    /**
     * Get documents by merchant ID and type
     *
     * @param string $merchantId The merchant's UUID
     * @param DocumentTypeEnum $documentType The document type
     * @return Collection<Document> Collection of document models
     */
    public function getDocumentsByMerchantIdAndType(string $merchantId, DocumentTypeEnum $documentType): Collection;
}
