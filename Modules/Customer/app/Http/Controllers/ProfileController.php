<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\ProfileUpdateRequest;
use Modules\Customer\Http\Resources\ProfileResource;
use Modules\Customer\Repositories\Profile\IProfileRepository;
use Modules\Customer\Services\Profile\IProfileService;

/**
 * Profile Controller
 *
 * Handles customer profile operations with auto-profile creation
 */
class ProfileController
{
    /**
     * Profile repository instance
     */
    protected IProfileRepository $profileRepository;

    /**
     * Profile service instance
     */
    protected IProfileService $profileService;

    /**
     * Constructor
     */
    public function __construct(
        IProfileRepository $profileRepository,
        IProfileService $profileService
    ) {
        $this->profileRepository = $profileRepository;
        $this->profileService = $profileService;
    }

    /**
     * Get or create customer profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        // Try to find existing profile
        $profile = $this->profileRepository->findByUserId($user->id);

        // If profile doesn't exist, create one automatically
        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        return response()->json([
            'status' => true,
            'message' => __('customer::profile.retrieved_successfully'),
            'data' => new ProfileResource($profile),
        ]);
    }

    /**
     * Update customer profile
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Get or create profile
        $profile = $this->profileRepository->findByUserId($user->id);

        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        // Update profile
        $updated = $this->profileService->updateProfile($profile->id, $validated);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('customer::profile.update_failed'),
            ], 500);
        }

        // Get updated profile
        $updatedProfile = $this->profileRepository->findById($profile->id);

        return response()->json([
            'status' => true,
            'message' => __('customer::profile.updated_successfully'),
            'data' => new ProfileResource($updatedProfile),
        ]);
    }
}
