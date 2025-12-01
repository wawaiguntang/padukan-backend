<?php

namespace Modules\Customer\Policies\DocumentUpload;

use App\Shared\Authorization\Repositories\IPolicyRepository;

class DocumentUploadPolicy implements IDocumentUploadPolicy
{
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(IPolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('customer.document.upload');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'max_file_size' => 10485760, // 10MB
                'allowed_mime_types' => [
                    'image/jpeg',
                    'image/png',
                    'image/jpg',
                    'image/gif',
                    'image/webp',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/plain',
                ],
                'require_verification' => true,
                'auto_process' => false,
                'storage_disk' => 'documents',
                'retention_days' => 365,
                'max_files_per_customer' => 20,
            ];
        }
    }

    /**
     * Check if file size is allowed
     */
    public function isFileSizeAllowed(int $fileSize): bool
    {
        return $fileSize <= $this->policySettings['max_file_size'];
    }

    /**
     * Get maximum file size
     */
    public function getMaxFileSize(): int
    {
        return $this->policySettings['max_file_size'] ?? 10485760;
    }

    /**
     * Check if MIME type is allowed
     */
    public function isMimeTypeAllowed(string $mimeType): bool
    {
        return in_array($mimeType, $this->policySettings['allowed_mime_types']);
    }

    /**
     * Get allowed MIME types
     */
    public function getAllowedMimeTypes(): array
    {
        return $this->policySettings['allowed_mime_types'] ?? [
            'image/jpeg',
            'image/png',
            'image/jpg',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ];
    }

    /**
     * Check if auto processing is enabled
     */
    public function isAutoProcessingEnabled(): bool
    {
        return $this->policySettings['auto_process'] ?? false;
    }

    /**
     * Get storage disk
     */
    public function getStorageDisk(): string
    {
        return $this->policySettings['storage_disk'] ?? 'documents';
    }

    /**
     * Get retention days
     */
    public function getRetentionDays(): int
    {
        return $this->policySettings['retention_days'] ?? 365;
    }

    /**
     * Get maximum files per customer
     */
    public function getMaxFilesPerCustomer(): int
    {
        return $this->policySettings['max_files_per_customer'] ?? 20;
    }

    /**
     * Check if verification is required
     */
    public function isVerificationRequired(): bool
    {
        return $this->policySettings['require_verification'] ?? true;
    }
}
