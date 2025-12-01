<?php

namespace Modules\Customer\Policies\DocumentOwnership;

interface IDocumentOwnershipPolicy
{
    /**
     * Check if user can access the document
     */
    public function canAccessDocument(string $userId, string $documentId): bool;

    /**
     * Check if user owns the document
     */
    public function ownsDocument(string $userId, string $documentId): bool;

    /**
     * Check if user can modify the document
     */
    public function canModifyDocument(string $userId, string $documentId): bool;

    /**
     * Check if user can delete the document
     */
    public function canDeleteDocument(string $userId, string $documentId): bool;
}
