<?php

namespace Modules\Driver\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\ProfileUpdateRequest;
use Modules\Driver\Http\Resources\ProfileResource;
use Modules\Driver\Services\Profile\IProfileService;
use Modules\Driver\Policies\ProfileOwnership\IProfileOwnershipPolicy;

/**
 * Update Profile Controller
 *
 * Handles updating driver profile
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
     * Update driver profile
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
        if (!$this->profileOwnershipPolicy->canModifyProfile($user->id, $profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::profile.modify_denied'),
            ], 403);
        }

        // Update profile
        $updated = $this->profileService->updateProfile($user->id, $validated);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('driver::profile.update_failed'),
            ], 500);
        }

        // Get updated profile
        $updatedProfile = $this->profileService->getProfileById($profile->id);

        return response()->json([
            'status' => true,
            'message' => __('driver::profile.updated_successfully'),
            'data' => new ProfileResource($updatedProfile),
        ]);
    }
}
