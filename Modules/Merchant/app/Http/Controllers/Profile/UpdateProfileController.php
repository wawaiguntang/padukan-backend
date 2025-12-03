<?php

namespace Modules\Merchant\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Http\Requests\ProfileUpdateRequest;
use Modules\Merchant\Http\Resources\ProfileResource;
use Modules\Merchant\Services\Profile\IProfileService;
use Modules\Merchant\Policies\ProfileOwnership\IProfileOwnershipPolicy;

/**
 * Update Profile Controller
 *
 * Handles updating merchant profile
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
     * Update merchant profile
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
                'message' => __('merchant::profile.modify_denied'),
            ], 403);
        }

        // Update profile
        $updated = $this->profileService->updateProfile($user->id, $validated);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::profile.update_failed'),
            ], 500);
        }

        // Get updated profile
        $updatedProfile = $this->profileService->getProfileByUserId($user->id);

        return response()->json([
            'status' => true,
            'message' => __('merchant::profile.updated_successfully'),
            'data' => new ProfileResource($updatedProfile),
        ]);
    }
}
