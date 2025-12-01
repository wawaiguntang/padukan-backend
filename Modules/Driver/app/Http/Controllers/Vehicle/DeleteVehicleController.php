<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Policies\VehicleOwnership\IVehicleOwnershipPolicy;

/**
 * Delete Vehicle Controller
 *
 * Handles deleting vehicle
 */
class DeleteVehicleController
{
    /**
     * Vehicle service instance
     */
    protected IVehicleService $vehicleService;

    /**
     * Vehicle ownership policy instance
     */
    protected IVehicleOwnershipPolicy $vehicleOwnershipPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IVehicleService $vehicleService,
        IVehicleOwnershipPolicy $vehicleOwnershipPolicy
    ) {
        $this->vehicleService = $vehicleService;
        $this->vehicleOwnershipPolicy = $vehicleOwnershipPolicy;
    }

    /**
     * Delete vehicle
     */
    public function __invoke(Request $request, string $id): JsonResponse
    {
        $user = $request->authenticated_user;

        $vehicle = $this->vehicleService->getVehicleById($id);

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.not_found'),
            ], 404);
        }

        // Check if user can delete this vehicle
        if (!$this->vehicleOwnershipPolicy->canDeleteVehicle($user->id, $vehicle->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.access_denied'),
            ], 403);
        }

        try {
            $deleted = $this->vehicleService->deleteVehicle($id);

            if (!$deleted) {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::vehicle.delete_failed'),
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => __('driver::vehicle.deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.delete_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
