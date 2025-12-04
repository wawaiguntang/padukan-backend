<?php

namespace Modules\Driver\Http\Controllers\DriverStatus;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Driver\Services\Profile\IProfileService;
use Modules\Driver\Services\DriverStatus\IDriverStatusService;
use Modules\Driver\Policies\DriverStatus\IDriverStatusPolicy;

/**
 * Get Driver Status Controller
 *
 * Handles retrieving driver status
 */
class GetDriverStatusController
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
     * Get driver status
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->authenticated_user;

        $profile = $this->profileService->getProfileByUserId($user->id);
        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.profile.not_found'),
            ], 404);
        }

        if (!$this->driverStatusPolicy->canViewStatus($user->id, $profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.status.access_denied'),
            ], 403);
        }

        $driverStatus = $this->driverStatusService->getOrCreateStatus($profile->id);

        $responseData = $driverStatus ? [
            'online_status' => $driverStatus->online_status->value,
            'operational_status' => $driverStatus->operational_status->value,
            'active_service' => $driverStatus->active_service?->value,
            'location' => $driverStatus->latitude && $driverStatus->longitude ? [
                'latitude' => $driverStatus->latitude,
                'longitude' => $driverStatus->longitude,
            ] : null,
            'last_updated_at' => $driverStatus->last_updated_at,
        ] : null;

        // Include vehicle information if driver is online and has a vehicle assigned
        if ($driverStatus && $driverStatus->online_status->value === 'online' && $driverStatus->vehicle) {
            $responseData['vehicle'] = [
                'id' => $driverStatus->vehicle->id,
                'type' => $driverStatus->vehicle->type->value,
                'brand' => $driverStatus->vehicle->brand,
                'model' => $driverStatus->vehicle->model,
                'license_plate' => $driverStatus->vehicle->license_plate,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => __('driver::controller.status.retrieved_successfully'),
            'data' => $responseData,
        ]);
    }
}
