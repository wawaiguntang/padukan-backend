<?php

namespace Modules\Customer\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\ProfileVerificationRequest;
use Modules\Customer\Enums\DocumentTypeEnum;
use Modules\Customer\Services\Profile\IProfileService;
use Modules\Customer\Services\Document\IDocumentService;
use Modules\Customer\Services\FileUpload\IFileUploadService;
use Modules\Customer\Policies\ProfileOwnership\IProfileOwnershipPolicy;

/**
 * Submit Profile Verification Controller
 *
 * Handles submitting profile verification with ID card
 */
class SubmitProfileVerificationController
{
    /**
     * Profile service instance
     */
    protected IProfileService $profileService;

    /**
     * Document service instance
     */
    protected IDocumentService $documentService;

    /**
     * File upload service instance
     */
    protected IFileUploadService $fileUploadService;

    /**
     * Profile ownership policy instance
     */
    protected IProfileOwnershipPolicy $profileOwnershipPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IProfileService $profileService,
        IDocumentService $documentService,
        IFileUploadService $fileUploadService,
        IProfileOwnershipPolicy $profileOwnershipPolicy
    ) {
        $this->profileService = $profileService;
        $this->documentService = $documentService;
        $this->fileUploadService = $fileUploadService;
        $this->profileOwnershipPolicy = $profileOwnershipPolicy;
    }

    /**
     * Submit profile verification with ID card
     */
    public function __invoke(ProfileVerificationRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        // Get or create profile via service
        $profile = $this->profileService->getProfileByUserId($user->id);
        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        // Check if user can submit verification
        if (!$this->profileOwnershipPolicy->canSubmitVerification($user->id, $profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('customer::profile.verification.cannot_submit'),
            ], 400);
        }

        try {
            // Upload documents directly
            $idCardDocument = $this->documentService->uploadDocument(
                $user->id,
                DocumentTypeEnum::ID_CARD,
                $request->file('id_card_file'),
                [
                    'meta' => $validated['id_card_meta'],
                    'expiry_date' => $validated['id_card_expiry_date'] ?? null,
                ]
            );

            $selfieDocument = $this->documentService->uploadDocument(
                $user->id,
                DocumentTypeEnum::SELFIE_WITH_KTP,
                $request->file('selfie_with_ktp_file'),
                [
                    'meta' => $validated['selfie_with_ktp_meta'] ?? null,
                ]
            );

            // Update profile verification status to pending via service
            $this->profileService->updateVerificationStatus($user->id, false, 'pending');

            $uploadedDocuments = [$idCardDocument, $selfieDocument];

            return response()->json([
                'status' => true,
                'message' => __('customer::profile.verification.submitted_successfully'),
                'data' => [
                    'verification_id' => $profile->id,
                    'status' => 'pending',
                    'documents_uploaded' => count($uploadedDocuments),
                    'documents' => array_map(function ($document) {
                        return [
                            'id' => $document->id,
                            'type' => $document->type,
                            'file_name' => $document->file_name,
                            'uploaded_at' => $document->created_at,
                            'temporary_url' => $this->fileUploadService->generateTemporaryUrl($document->file_path),
                        ];
                    }, $uploadedDocuments),
                    'submitted_at' => now(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('customer::profile.verification.submission_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
