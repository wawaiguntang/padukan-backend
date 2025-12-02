<?php

namespace Modules\Customer\Policies\DocumentStatus;

use App\Shared\Authorization\Repositories\IPolicyRepository;

class DocumentStatusPolicy implements IDocumentStatusPolicy
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
        $settings = $this->policyRepository->getSetting('customer.document.status');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'allowed_update_statuses' => [],
                'allowed_delete_statuses' => [],
                'allowed_resubmit_statuses' => ['rejected'],
                'auto_reject_expired' => true,
                'expiry_grace_days' => 30,
            ];
        }
    }

    /**
     * Check if document can be updated based on status
     */
    public function canUpdateDocument(string $documentId, string $currentStatus): bool
    {
        return in_array($currentStatus, $this->policySettings['allowed_update_statuses']);
    }

    /**
     * Check if document can be deleted based on status
     */
    public function canDeleteDocument(string $documentId, string $currentStatus): bool
    {
        return in_array($currentStatus, $this->policySettings['allowed_delete_statuses']);
    }

    /**
     * Check if document can be resubmitted based on status
     */
    public function canResubmitDocument(string $documentId, string $currentStatus): bool
    {
        return in_array($currentStatus, $this->policySettings['allowed_resubmit_statuses']);
    }

    /**
     * Check if expired documents should be auto-rejected
     */
    public function shouldAutoRejectExpired(): bool
    {
        return $this->policySettings['auto_reject_expired'] ?? true;
    }

    /**
     * Get grace days for expiry
     */
    public function getExpiryGraceDays(): int
    {
        return $this->policySettings['expiry_grace_days'] ?? 30;
    }
}
