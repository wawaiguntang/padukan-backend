<?php

namespace Modules\Profile\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Profile\Services\Driver\IDriverService;
use Modules\Profile\Http\Resources\ProfileResponseResource;

/**
 * Driver Vehicle Controller
 *
 * Handles driver vehicle business logic
 */
class DriverVehicleController extends Controller
{
    private IDriverService $driverService;

    public function __construct(IDriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Get driver vehicles
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function getVehicles(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->driverService->getDriverVehicles($userId);

        return new ProfileResponseResource($result, 'vehicles_retrieved');
    }

    /**
     * Register new vehicle with documents
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function registerVehicle(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        // Create vehicle
        $vehicle = $this->driverService->createDriverVehicle($userId, [
            'type' => $data['vehicle_type'],
            'brand' => $data['brand'],
            'model' => $data['model'],
            'year' => $data['year'],
            'color' => $data['color'],
            'license_plate' => $data['license_plate']
        ]);

        // Upload STNK document if provided
        if (isset($data['stnk_file'])) {
            $this->driverService->createDriverDocument($userId, [
                'type' => 'stnk',
                'file' => $data['stnk_file'],
                'meta' => [
                    'number' => $data['stnk_number'] ?? null,
                    'expiry_date' => $data['stnk_expiry_date'] ?? null
                ]
            ]);
        }

        // Upload vehicle photo if provided
        if (isset($data['vehicle_photo_file'])) {
            $this->driverService->createDriverDocument($userId, [
                'type' => 'vehicle_photo',
                'file' => $data['vehicle_photo_file']
            ]);
        }

        return new ProfileResponseResource([
            'vehicle' => $vehicle,
            'message' => 'Vehicle registered successfully with documents'
        ], 'vehicle_registered');
    }

    /**
     * Update vehicle information
     *
     * @param Request $request
     * @param string $vehicleId
     * @return ProfileResponseResource
     */
    public function updateVehicle(Request $request, string $vehicleId): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        $vehicle = $this->driverService->updateDriverVehicle($userId, $vehicleId, $data);

        return new ProfileResponseResource([
            'vehicle' => $vehicle
        ], 'vehicle_updated');
    }

    /**
     * Remove vehicle
     *
     * @param Request $request
     * @param string $vehicleId
     * @return ProfileResponseResource
     */
    public function removeVehicle(Request $request, string $vehicleId): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->driverService->deleteDriverVehicle($userId, $vehicleId);

        return new ProfileResponseResource([
            'deleted' => $result
        ], 'vehicle_removed');
    }
}