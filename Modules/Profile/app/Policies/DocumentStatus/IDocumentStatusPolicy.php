<?php

namespace Modules\Profile\Policies\ProfileOwnership;

interface IDocumentStatusPolicy
{
    /**
     * Check if document can be updated based on status
     */
    public function canUpdateDocument(string $documentId, string $currentStatus): bool;

    /**
     * Check if document can be deleted based on status
     */
    public function canDeleteDocument(string $documentId, string $currentStatus): bool;

    /**
     * Check if document can be resubmitted based on status
     */
    public function canResubmitDocument(string $documentId, string $currentStatus): bool;

    /**
     * Check if expired documents should be auto-rejected
     */
    public function shouldAutoRejectExpired(): bool;

    /**
     * Get grace days for expiry
     */
    public function getExpiryGraceDays(): int;
}