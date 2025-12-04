<?php

namespace Modules\Driver\Http\Controllers\DriverStatus;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\UpdateOnlineStatusRequest;
use Modules\Driver\Services\Profile\IProfileService;
use Modules\Driver\Services\DriverStatus\IDriverStatusService;
use Modules\Driver\Policies\DriverStatus\IDriverStatusPolicy;

/**
 * Update Online Status Controller
 *
 * Handles updating driver online status
 */
class UpdateOnlineStatusController
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
     * Update online status
     */
    public function __invoke(UpdateOnlineStatusRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $onlineStatus = $request->input('online_status');
        $activeService = $request->input('active_service');
        $vehicleId = $request->input('vehicle_id');

        $profile = $this->profileService->getProfileByUserId($user->id);
        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.profile.not_found'),
            ], 404);
        }

        if (!$this->driverStatusPolicy->canUpdateOnlineStatus($user->id, $profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.status.cannot_update_online_status'),
            ], 403);
        }

        // When going online, validate vehicle ownership and service compatibility
        if ($onlineStatus === 'online' && $activeService && $vehicleId) {
            // Check if vehicle belongs to user and is verified
            $vehicle = $profile->vehicles()->where('id', $vehicleId)->where('is_verified', true)->first();
            if (!$vehicle) {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::controller.status.vehicle_not_found_or_not_verified'),
                ], 404);
            }

            // Check if vehicle type is compatible with the service
            if (!$this->driverStatusPolicy->canUseServiceWithVehicles($user->id, $profile->id, $activeService)) {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::controller.status.service_not_available_for_vehicles'),
                ], 403);
            }

            // Check general active service permission
            if (!$this->driverStatusPolicy->canSetActiveService($user->id, $profile->id, $activeService)) {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::controller.status.cannot_set_active_service'),
                ], 403);
            }
        }

        try {
            $driverStatus = $this->driverStatusService->updateOnlineStatus($profile->id, $onlineStatus);

            // If going online and active service is provided, also update the active service and vehicle
            if ($onlineStatus === 'online' && $activeService) {
                $updateData = ['active_service' => $activeService];
                if ($vehicleId) {
                    $updateData['vehicle_id'] = $vehicleId;
                }
                $driverStatus = $this->driverStatusService->updateStatus($profile->id, $updateData);
            }

            $responseData = [
                'online_status' => $driverStatus->online_status->value,
                'updated_at' => $driverStatus->last_updated_at,
            ];

            // Include active service and vehicle info in response if it was updated
            if ($onlineStatus === 'online' && $activeService) {
                $responseData['active_service'] = $driverStatus->active_service?->value;
                if ($driverStatus->vehicle) {
                    $responseData['vehicle'] = [
                        'id' => $driverStatus->vehicle->id,
                        'type' => $driverStatus->vehicle->type->value,
                        'brand' => $driverStatus->vehicle->brand,
                        'model' => $driverStatus->vehicle->model,
                        'license_plate' => $driverStatus->vehicle->license_plate,
                    ];
                }
            }

            return response()->json([
                'status' => true,
                'message' => __('driver::controller.status.online_status_updated'),
                'data' => $responseData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.status.online_status_update_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
