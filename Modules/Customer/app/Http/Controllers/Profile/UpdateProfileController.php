<?php

namespace Modules\Customer\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Customer\Http\Requests\ProfileUpdateRequest;
use Modules\Customer\Http\Resources\ProfileResource;
use Modules\Customer\Services\Profile\IProfileService;
use Modules\Customer\Policies\ProfileOwnership\IProfileOwnershipPolicy;

/**
 * Update Profile Controller
 *
 * Handles updating customer profile
 */
class UpdateProfileController
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
     * Update customer profile
     */
    public function __invoke(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        $profile = $this->profileService->getProfileByUserId($user->id);

        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        // Check if user can modify this profile
        if (!$this->profileOwnershipPolicy->ownsProfile($user->id, $profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('customer::profile.modify_denied'),
            ], 403);
        }

        // Update profile
        $updated = $this->profileService->updateProfile($user->id, $validated);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('customer::profile.update_failed'),
            ], 500);
        }

        // Get updated profile
        $updatedProfile = $this->profileService->getProfileByUserId($user->id);

        return response()->json([
            'status' => true,
            'message' => __('customer::profile.updated_successfully'),
            'data' => new ProfileResource($updatedProfile),
        ]);
    }
}
