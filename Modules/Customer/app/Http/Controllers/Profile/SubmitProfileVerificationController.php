<?php

namespace Modules\Customer\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\ProfileVerificationRequest;
use Modules\Customer\Services\Profile\IProfileService;
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
     * Profile ownership policy instance
     */
    protected IProfileOwnershipPolicy $profileOwnershipPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IProfileService $profileService,
        IProfileOwnershipPolicy $profileOwnershipPolicy
    ) {
        $this->profileService = $profileService;
        $this->profileOwnershipPolicy = $profileOwnershipPolicy;
    }

    /**
     * Submit profile verification with ID card
     */
    public function __invoke(ProfileVerificationRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        $profile = $this->profileService->getProfileByUserId($user->id);
        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        if ($profile->verification_status !== \Modules\Customer\Enums\VerificationStatusEnum::PENDING) {
            return response()->json([
                'status' => false,
                'message' => __('customer::profile.verification.cannot_submit'),
            ], 400);
        }

        try {
            $result = $this->profileService->resubmitVerification($user->id, [
                'id_card_file' => $request->file('id_card_file'),
                'id_card_meta' => $validated['id_card_meta'],
                'id_card_expiry_date' => $validated['id_card_expiry_date'] ?? null,
                'selfie_with_id_card_file' => $request->file('selfie_with_id_card_file'),
                'selfie_with_id_card_meta' => $validated['selfie_with_id_card_meta'] ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => __('customer::profile.verification.submitted_successfully'),
                'data' => $result,
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
