<?php

namespace Modules\Customer\Policies\DocumentUpload;

interface IDocumentUploadPolicy
{
    /**
     * Check if file size is allowed
     */
    public function isFileSizeAllowed(int $fileSize): bool;

    /**
     * Get maximum file size
     */
    public function getMaxFileSize(): int;

    /**
     * Check if MIME type is allowed
     */
    public function isMimeTypeAllowed(string $mimeType): bool;

    /**
     * Get allowed MIME types
     */
    public function getAllowedMimeTypes(): array;

    /**
     * Check if auto processing is enabled
     */
    public function isAutoProcessingEnabled(): bool;

    /**
     * Get storage disk
     */
    public function getStorageDisk(): string;

    /**
     * Get retention days
     */
    public function getRetentionDays(): int;

    /**
     * Get maximum files per customer
     */
    public function getMaxFilesPerCustomer(): int;

    /**
     * Check if verification is required
     */
    public function isVerificationRequired(): bool;
}
