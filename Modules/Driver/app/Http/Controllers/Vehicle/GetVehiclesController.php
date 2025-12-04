<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Driver\Services\Vehicle\IVehicleService;

/**
 * Get Vehicles Controller
 *
 * Handles retrieving driver vehicles with verification status
 */
class GetVehiclesController
{
    /**
     * Vehicle service instance
     */
    protected IVehicleService $vehicleService;

    /**
     * Constructor
     */
    public function __construct(IVehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Get driver vehicles with verification status
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->authenticated_user;

        $vehicles = $this->vehicleService->getVehiclesByUserId($user->id);

        return response()->json([
            'status' => true,
            'message' => __('driver::controller.vehicle.retrieved_successfully'),
            'data' => $vehicles->map(function ($vehicle) {
                return [
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
                ];
            }),
        ]);
    }
}
