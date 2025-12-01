<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Policies\VehicleOwnership\IVehicleOwnershipPolicy;

/**
 * Get Vehicle Controller
 *
 * Handles retrieving specific vehicle
 */
class GetVehicleController
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
     * Get specific vehicle
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

        // Check if user can access this vehicle
        if (!$this->vehicleOwnershipPolicy->canAccessVehicle($user->id, $vehicle->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.access_denied'),
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => __('driver::vehicle.retrieved_successfully'),
            'data' => [
                'id' => $vehicle->id,
                'driver_profile_id' => $vehicle->driver_profile_id,
                'type' => $vehicle->type,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'year' => $vehicle->year,
                'color' => $vehicle->color,
                'license_plate' => $vehicle->license_plate,
                'is_verified' => $vehicle->is_verified,
                'verification_status' => $vehicle->verification_status,
                'created_at' => $vehicle->created_at,
                'updated_at' => $vehicle->updated_at,
            ],
        ]);
    }
}
