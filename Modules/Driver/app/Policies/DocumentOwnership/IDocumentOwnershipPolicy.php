<?php

namespace Modules\Driver\Policies\DocumentOwnership;

interface IDocumentOwnershipPolicy
{
    /**
     * Check if user owns the document
     */
    public function ownsDocument(string $userId, string $documentId): bool;

    /**
     * Check if user can access document data
     */
    public function canAccessDocument(string $userId, string $documentId): bool;

    /**
     * Check if user can modify document data
     */
    public function canModifyDocument(string $userId, string $documentId): bool;

    /**
     * Check if user can delete document
     */
    public function canDeleteDocument(string $userId, string $documentId): bool;

    /**
     * Check if user can upload document for verification
     */
    public function canUploadVerificationDocument(string $userId, string $profileId, string $documentType): bool;
}
