<?php

namespace Modules\Driver\Repositories\Document;

use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Models\Document;

/**
 * Interface for Document Repository
 *
 * This interface defines the contract for document data operations
 * in the driver module.
 */
interface IDocumentRepository
{
    /**
     * Create a new document
     *
     * @param array $data Document data containing:
     * - profile_id: string - Profile's UUID
     * - type: DriverDocumentTypeEnum - Document type
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
     * Find documents by type and profile ID
     *
     * @param string $profileId The profile's UUID
     * @param DocumentTypeEnum $type The document type
     * @return Collection<Document> Collection of document models
     */
    public function findByTypeAndProfileId(string $profileId, DocumentTypeEnum $type): Collection;
}
