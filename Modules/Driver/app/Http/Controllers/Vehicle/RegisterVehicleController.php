<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\VehicleCreateRequest;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Policies\VehicleManagement\IVehicleManagementPolicy;

/**
 * Register Vehicle Controller
 *
 * Handles registering vehicle basic information
 */
class RegisterVehicleController
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
     * Register vehicle basic information
     */
    public function __invoke(VehicleCreateRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        try {
            if (!$this->vehicleManagementPolicy->canRegisterVehicleType($user->id, $request->type)) {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::controller.vehicle.limit_exceeded'),
                    'data' => [
                        'max_vehicles' => $this->vehicleManagementPolicy->getMaxVehiclesPerDriver(),
                        'current_count' => $this->vehicleService->getVehiclesByUserId($user->id)->count(),
                    ],
                ], 400);
            }

            // Create vehicle
            $vehicle = $this->vehicleService->createVehicle($user->id, $validated);

            return response()->json([
                'status' => true,
                'message' => __('driver::controller.vehicle.registered_successfully'),
                'data' => [
                    'id' => $vehicle->id,
                    'type' => $vehicle->type,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'year' => $vehicle->year,
                    'color' => $vehicle->color,
                    'license_plate' => $vehicle->license_plate,
                    'is_verified' => $vehicle->is_verified,
                    'verification_status' => $vehicle->verification_status,
                    'registered_at' => $vehicle->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.vehicle.registration_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
