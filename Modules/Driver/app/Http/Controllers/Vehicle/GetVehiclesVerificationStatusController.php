<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Driver\Services\Vehicle\IVehicleService;

/**
 * Get Vehicles Verification Status Controller
 *
 * Handles getting overall vehicle verification status
 */
class GetVehiclesVerificationStatusController
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
     * Get vehicle verification status
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->authenticated_user;

        $vehicles = $this->vehicleService->getVehiclesByUserId($user->id);

        $verificationStats = [
            'total' => $vehicles->count(),
            'pending' => $vehicles->where('verification_status', 'pending')->count(),
            'verified' => $vehicles->where('is_verified', true)->count(),
            'rejected' => $vehicles->where('verification_status', 'rejected')->count(),
        ];

        $vehiclesByStatus = [
            'pending' => $vehicles->where('verification_status', 'pending')->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'type' => $vehicle->type,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'license_plate' => $vehicle->license_plate,
                    'submitted_at' => $vehicle->updated_at,
                ];
            }),
            'verified' => $vehicles->where('is_verified', true)->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'type' => $vehicle->type,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'license_plate' => $vehicle->license_plate,
                    'verified_at' => $vehicle->updated_at,
                ];
            }),
            'rejected' => $vehicles->where('verification_status', 'rejected')->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'type' => $vehicle->type,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'license_plate' => $vehicle->license_plate,
                    'rejected_at' => $vehicle->updated_at,
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'message' => __('driver::vehicle.verification_status_retrieved'),
            'data' => [
                'statistics' => $verificationStats,
                'vehicles' => $vehiclesByStatus,
            ],
        ]);
    }
}
