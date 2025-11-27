<?php

namespace Modules\Profile\Policies\DataManagement;

use Modules\Authorization\Repositories\Policy\IPolicyRepository;

class DocumentUploadPolicy implements IDocumentUploadPolicy
{
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(IPolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('profile.document_upload');
        $this->policySettings = $settings ?: [
            'enabled' => true,
            'max_file_size' => 5242880, // 5MB
            'allowed_mime_types' => [
                'image/jpeg', 'image/png', 'image/jpg',
                'application/pdf'
            ],
            'require_verification' => true,
            'auto_process' => false,
            'storage_disk' => 'documents',
            'retention_days' => 365,
        ];
    }

    public function isFileSizeAllowed(int $fileSize): bool
    {
        if (!$this->policySettings['enabled']) return true;
        return $fileSize <= $this->policySettings['max_file_size'];
    }

    public function getMaxFileSize(): int
    {
        return $this->policySettings['max_file_size'] ?? 5242880;
    }

    public function isMimeTypeAllowed(string $mimeType): bool
    {
        if (!$this->policySettings['enabled']) return true;
        return in_array($mimeType, $this->policySettings['allowed_mime_types']);
    }

    public function getAllowedMimeTypes(): array
    {
        return $this->policySettings['allowed_mime_types'] ?? [
            'image/jpeg', 'image/png', 'image/jpg', 'application/pdf'
        ];
    }

    public function isAutoProcessingEnabled(): bool
    {
        return $this->policySettings['auto_process'] ?? false;
    }

    public function getStorageDisk(): string
    {
        return $this->policySettings['storage_disk'] ?? 'documents';
    }

    public function getRetentionDays(): int
    {
        return $this->policySettings['retention_days'] ?? 365;
    }
}