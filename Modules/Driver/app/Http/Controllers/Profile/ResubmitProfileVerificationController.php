<?php

namespace Modules\Driver\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\ProfileVerificationRequest;
use Modules\Driver\Services\Profile\IProfileService;
use Modules\Driver\Policies\ProfileOwnership\IProfileOwnershipPolicy;
use Modules\Driver\Repositories\Profile\IProfileRepository;

/**
 * Resubmit Profile Verification Controller
 *
 * Handles resubmitting profile verification (only if rejected)
 */
class ResubmitProfileVerificationController
{
    /**
     * Profile service instance
     */
    protected IProfileService $profileService;

    /**
     * Profile repository instance
     */
    protected IProfileRepository $profileRepository;

    /**
     * Profile ownership policy instance
     */
    protected IProfileOwnershipPolicy $profileOwnershipPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IProfileService $profileService,
        IProfileRepository $profileRepository,
        IProfileOwnershipPolicy $profileOwnershipPolicy
    ) {
        $this->profileService = $profileService;
        $this->profileRepository = $profileRepository;
        $this->profileOwnershipPolicy = $profileOwnershipPolicy;
    }

    /**
     * Resubmit profile verification (only if rejected)
     */
    public function __invoke(ProfileVerificationRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        try {
            $profile = $this->profileRepository->findByUserId($user->id);

            if (!$profile) {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::profile.not_found'),
                ], 404);
            }

            if (!$this->profileOwnershipPolicy->canResubmitVerification($user->id, $profile->id)) {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::profile.verification.resubmit_not_allowed'),
                ], 400);
            }

            $result = $this->profileService->resubmitVerification($user->id, [
                'id_card_file' => $request->file('id_card_file'),
                'id_card_meta' => $validated['id_card_meta'],
                'id_card_expiry_date' => $validated['id_card_expiry_date'] ?? null,
                'selfie_with_id_card_file' => $request->file('selfie_with_id_card_file'),
                'selfie_with_id_card_meta' => $validated['selfie_with_id_card_meta'] ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => __('driver::profile.verification.resubmitted_successfully'),
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::profile.verification.resubmission_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
