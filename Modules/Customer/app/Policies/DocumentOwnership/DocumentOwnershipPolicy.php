<?php

namespace Modules\Customer\Policies\DocumentOwnership;

use Modules\Customer\Repositories\Profile\IProfileRepository;
use Modules\Customer\Repositories\Document\IDocumentRepository;
use Modules\Authorization\Repositories\Policy\IPolicyRepository;

class DocumentOwnershipPolicy implements IDocumentOwnershipPolicy
{
    private IProfileRepository $profileRepository;
    private IDocumentRepository $documentRepository;
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(
        IProfileRepository $profileRepository,
        IDocumentRepository $documentRepository,
        IPolicyRepository $policyRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->documentRepository = $documentRepository;
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('profile.document_ownership');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'enabled' => true,
                'strict_ownership' => true,
                'check_user_active' => true,
                'allow_admin_override' => false,
            ];
        }
    }

    /**
     * Check if user can access the document
     */
    public function canAccessDocument(string $userId, string $documentId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        return $this->ownsDocument($userId, $documentId);
    }

    /**
     * Check if user owns the document
     */
    public function ownsDocument(string $userId, string $documentId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        $document = $this->documentRepository->findById($documentId);

        if (!$document) {
            return false;
        }

        // Check if document belongs to the user's profile
        return $document->profile_id === $profile->id;
    }

    /**
     * Check if user can modify the document
     */
    public function canModifyDocument(string $userId, string $documentId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        return $this->ownsDocument($userId, $documentId);
    }

    /**
     * Check if user can delete the document
     */
    public function canDeleteDocument(string $userId, string $documentId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        return $this->ownsDocument($userId, $documentId);
    }
}
