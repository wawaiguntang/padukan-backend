<?php

namespace Modules\Customer\Http\Controllers\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Resources\ProfileResource;
use Modules\Customer\Services\Profile\IProfileService;
use Modules\Customer\Policies\ProfileOwnership\IProfileOwnershipPolicy;

/**
 * Get Profile Controller
 *
 * Handles retrieving customer profile
 */
class GetProfileController
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
     * Get or create customer profile
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->authenticated_user;

        $profile = $this->profileService->getProfileByUserId($user->id);

        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        // Check if user can access this profile
        if (!$this->profileOwnershipPolicy->ownsProfile($user->id, $profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('customer::controller.profile.access_denied'),
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => __('customer::controller.profile.retrieved_successfully'),
            'data' => new ProfileResource($profile),
        ]);
    }
}
