<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\VehicleUpdateRequest;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Policies\VehicleManagement\IVehicleManagementPolicy;

/**
 * Update Vehicle Controller
 *
 * Handles updating vehicle (only allowed if rejected)
 */
class UpdateVehicleController
{
    /**
     * Vehicle service instance
     */
    protected IVehicleService $vehicleService;

    /**
     * Vehicle ownership policy instance
     */
    protected IVehicleManagementPolicy $vehicleManagementPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IVehicleService $vehicleService,
        IVehicleManagementPolicy $vehicleManagementPolicy
    ) {
        $this->vehicleService = $vehicleService;
        $this->vehicleManagementPolicy = $vehicleManagementPolicy;
    }

    /**
     * Update vehicle (only allowed if rejected)
     */
    public function __invoke(VehicleUpdateRequest $request, string $id): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        $vehicle = $this->vehicleService->getVehicleById($id);

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.vehicle.not_found'),
            ], 404);
        }

        if($vehicle->verification_status === \Modules\Driver\Enums\VerificationStatusEnum::APPROVED) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.vehicle.update_not_allowed'),
            ], 400);
        }

        if (!$this->vehicleManagementPolicy->ownsVehicle($user->id, $vehicle->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.vehicle.access_denied'),
            ], 403);
        }

        try {
            $updated = $this->vehicleService->updateVehicle($id, $validated);

            if (!$updated) {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::controller.vehicle.update_failed'),
                ], 500);
            }

            // Reset verification status after update
            $this->vehicleService->updateVerificationStatus($id, false, 'pending');

            $updatedVehicle = $this->vehicleService->getVehicleById($id);

            return response()->json([
                'status' => true,
                'message' => __('driver::controller.vehicle.updated_successfully'),
                'data' => [
                    'id' => $updatedVehicle->id,
                    'driver_profile_id' => $updatedVehicle->driver_profile_id,
                    'type' => $updatedVehicle->type,
                    'brand' => $updatedVehicle->brand,
                    'model' => $updatedVehicle->model,
                    'year' => $updatedVehicle->year,
                    'color' => $updatedVehicle->color,
                    'license_plate' => $updatedVehicle->license_plate,
                    'is_verified' => $updatedVehicle->is_verified,
                    'verification_status' => $updatedVehicle->verification_status,
                    'created_at' => $updatedVehicle->created_at,
                    'updated_at' => $updatedVehicle->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.vehicle.update_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
