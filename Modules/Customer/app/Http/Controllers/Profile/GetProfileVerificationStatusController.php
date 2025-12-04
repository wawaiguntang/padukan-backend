<?php

namespace Modules\Customer\Http\Controllers\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Services\Profile\IProfileService;
use Modules\Customer\Policies\ProfileOwnership\IProfileOwnershipPolicy;

/**
 * Get Profile Verification Status Controller
 *
 * Handles checking profile verification status
 */
class GetProfileVerificationStatusController
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
     * Check profile verification status
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->authenticated_user;

        $profile = $this->profileService->getProfileByUserId($user->id);

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => __('customer::controller.profile.not_found'),
            ], 404);
        }

        // Check if user can access this profile
        if (!$this->profileOwnershipPolicy->ownsProfile($user->id, $profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('customer::controller.profile.access_denied'),
            ], 403);
        }

        $verificationData = $this->profileService->getVerificationStatus($user->id);

        return response()->json([
            'status' => true,
            'message' => __('customer::controller.profile.verification.status_retrieved'),
            'data' => $verificationData,
        ]);
    }
}
