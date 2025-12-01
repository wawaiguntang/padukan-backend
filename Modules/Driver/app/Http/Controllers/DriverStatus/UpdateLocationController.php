<?php

namespace Modules\Driver\Http\Controllers\DriverStatus;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\UpdateLocationRequest;
use Modules\Driver\Services\Profile\IProfileService;
use Modules\Driver\Services\DriverStatus\IDriverStatusService;
use Modules\Driver\Policies\DriverStatus\IDriverStatusPolicy;

/**
 * Update Location Controller
 *
 * Handles updating driver location
 */
class UpdateLocationController
{
    /**
     * Profile service instance
     */
    protected IProfileService $profileService;

    /**
     * Driver status service instance
     */
    protected IDriverStatusService $driverStatusService;

    /**
     * Driver status policy instance
     */
    protected IDriverStatusPolicy $driverStatusPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IProfileService $profileService,
        IDriverStatusService $driverStatusService,
        IDriverStatusPolicy $driverStatusPolicy
    ) {
        $this->profileService = $profileService;
        $this->driverStatusService = $driverStatusService;
        $this->driverStatusPolicy = $driverStatusPolicy;
    }

    /**
     * Update location
     */
    public function __invoke(UpdateLocationRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $profile = $this->profileService->getProfileByUserId($user->id);
        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => __('driver::profile.not_found'),
            ], 404);
        }

        if (!$this->driverStatusPolicy->canUpdateLocation($user->id, $profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::status.cannot_update_location'),
            ], 403);
        }

        try {
            $driverStatus = $this->driverStatusService->updateLocation($profile->id, $latitude, $longitude);

            return response()->json([
                'status' => true,
                'message' => __('driver::status.location_updated'),
                'data' => [
                    'location' => [
                        'latitude' => $driverStatus->latitude,
                        'longitude' => $driverStatus->longitude,
                    ],
                    'updated_at' => $driverStatus->last_updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::status.update_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
