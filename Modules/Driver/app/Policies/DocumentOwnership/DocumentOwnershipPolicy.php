<?php

namespace Modules\Driver\Policies\DocumentOwnership;

use Modules\Driver\Repositories\Document\IDocumentRepository;
use Modules\Driver\Repositories\Profile\IProfileRepository;
use App\Shared\Setting\Services\ISettingService;

class DocumentOwnershipPolicy implements IDocumentOwnershipPolicy
{
    private IDocumentRepository $documentRepository;
    private IProfileRepository $profileRepository;
    private ISettingService $settingService;
    private array $policySettings;

    public function __construct(
        IDocumentRepository $documentRepository,
        IProfileRepository $profileRepository,
        ISettingService $settingService
    ) {
        $this->documentRepository = $documentRepository;
        $this->profileRepository = $profileRepository;
        $this->settingService = $settingService;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->settingService->getSettingByKey('driver.document.verification_upload');

        if (!empty($settings)) {
            $this->policySettings = $settings['value'] ?? [];
        } else {
            // Fallback to default
            $this->policySettings = [
                'require_verification' => true,
            ];
        }
    }

    /**
     * Check if user owns the document
     */
    public function ownsDocument(string $userId, string $documentId): bool
    {
        $document = $this->documentRepository->findById($documentId);

        if (!$document) {
            return false;
        }

        // Check if document belongs to user's profile or vehicle
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        // Check if document belongs to profile
        if (
            $document->documentable_type === 'Modules\Driver\Models\Profile' &&
            $document->documentable_id === $profile->id
        ) {
            return true;
        }

        // Check if document belongs to one of user's vehicles
        if ($document->documentable_type === 'Modules\Driver\Models\Vehicle') {
            $vehicle = $profile->vehicles()->where('id', $document->documentable_id)->first();
            return $vehicle !== null;
        }

        return false;
    }

    /**
     * Check if user can access document data
     */
    public function canAccessDocument(string $userId, string $documentId): bool
    {
        return $this->ownsDocument($userId, $documentId);
    }

    /**
     * Check if user can modify document data
     */
    public function canModifyDocument(string $userId, string $documentId): bool
    {
        return $this->ownsDocument($userId, $documentId);
    }

    /**
     * Check if user can delete document
     */
    public function canDeleteDocument(string $userId, string $documentId): bool
    {
        return $this->ownsDocument($userId, $documentId);
    }

    /**
     * Check if user can upload document for verification
     */
    public function canUploadVerificationDocument(string $userId, string $profileId, string $documentType): bool
    {
        // Check if user owns the profile
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile || $profile->id !== $profileId) {
            return false;
        }

        // Check if verification is required
        if (!$this->policySettings['require_verification']) {
            return true;
        }

        // Additional checks can be added here based on document type
        // For example, check if user has already uploaded this type of document

        return true;
    }
}
