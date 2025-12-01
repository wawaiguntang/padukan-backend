<?php

namespace Modules\Customer\Repositories\Document;

use Illuminate\Database\Eloquent\Collection;
use Modules\Customer\Enums\DocumentTypeEnum;
use Modules\Customer\Enums\VerificationStatusEnum;
use Modules\Customer\Models\Document;

/**
 * Interface for Document Repository
 *
 * This interface defines the contract for document data operations
 * in the customer module.
 */
interface IDocumentRepository
{
    /**
     * Find documents by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return Collection<Document> Collection of document models
     */
    public function findByProfileId(string $profileId): Collection;

    /**
     * Find a document by ID
     *
     * @param string $id The document's UUID
     * @return Document|null The document model if found, null otherwise
     */
    public function findById(string $id): ?Document;

    /**
     * Create a new document
     *
     * @param array $data Document data containing:
     * - profile_id: string - Profile's UUID
     * - type: DocumentTypeEnum - Document type
     * - file_path: string - File path
     * - file_name: string - Original file name
     * - mime_type: string - File MIME type
     * - file_size: int - File size in bytes
     * - meta?: array - Additional metadata (optional)
     * - expiry_date?: string - Expiry date (optional)
     * - verification_status?: VerificationStatusEnum - Verification status (optional)
     * @return Document The created document model
     */
    public function create(array $data): Document;

    /**
     * Update an existing document
     *
     * @param string $id The document's UUID
     * @param array $data Document data to update (same structure as create)
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a document
     *
     * @param string $id The document's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Update document verification status
     *
     * @param string $id The document's UUID
     * @param VerificationStatusEnum $status The new verification status
     * @param string|null $verifiedBy The user who verified (optional)
     * @return bool True if update was successful, false otherwise
     */
    public function updateVerificationStatus(string $id, VerificationStatusEnum $status, ?string $verifiedBy = null): bool;

    /**
     * Find documents by type and profile ID
     *
     * @param string $profileId The profile's UUID
     * @param DocumentTypeEnum $type The document type
     * @return Collection<Document> Collection of document models
     */
    public function findByTypeAndProfileId(string $profileId, DocumentTypeEnum $type): Collection;

    /**
     * Check if document exists by ID
     *
     * @param string $id The document's UUID
     * @return bool True if document exists, false otherwise
     */
    public function existsById(string $id): bool;
}
