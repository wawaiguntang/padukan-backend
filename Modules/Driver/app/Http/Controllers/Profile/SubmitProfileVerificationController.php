<?php

namespace Modules\Driver\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\ProfileVerificationRequest;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Services\Profile\IProfileService;
use Modules\Driver\Services\Document\IDocumentService;
use Modules\Driver\Services\FileUpload\IFileUploadService;
use Modules\Driver\Policies\ProfileOwnership\IProfileOwnershipPolicy;

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

        try {
            if ($profile->verification_status === \Modules\Driver\Enums\VerificationStatusEnum::PENDING) {
                $result = $this->profileService->resubmitVerification($user->id, [
                    'id_card_file' => $request->file('id_card_file'),
                    'id_card_meta' => $validated['id_card_meta'],
                    'id_card_expiry_date' => $validated['id_card_expiry_date'] ?? null,
                    'selfie_with_id_card_file' => $request->file('selfie_with_id_card_file'),
                    'selfie_with_id_card_meta' => $validated['selfie_with_id_card_meta'] ?? null,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => __('driver::controller.profile.verification.submitted_successfully'),
                    'data' => $result,
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::controller.profile.verification.cannot_submit'),
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.profile.verification.submission_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
