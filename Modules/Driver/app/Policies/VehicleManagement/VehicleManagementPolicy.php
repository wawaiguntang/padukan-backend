<?php

namespace Modules\Driver\Policies\VehicleManagement;

use Modules\Driver\Repositories\Vehicle\IVehicleRepository;
use Modules\Driver\Repositories\Profile\IProfileRepository;
use App\Shared\Setting\Services\ISettingService;

class VehicleManagementPolicy implements IVehicleManagementPolicy
{
    private IVehicleRepository $vehicleRepository;
    private IProfileRepository $profileRepository;
    private ISettingService $settingService;
    private array $policySettings;

    public function __construct(
        IVehicleRepository $vehicleRepository,
        IProfileRepository $profileRepository,
        ISettingService $settingService
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->profileRepository = $profileRepository;
        $this->settingService = $settingService;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $this->policySettings = $this->settingService->getSettingByKey('driver.vehicle.management');
    }

    /**
     * Check if user owns the vehicle
     */
    public function ownsVehicle(string $userId, string $vehicleId): bool
    {
        $vehicle = $this->vehicleRepository->findById($vehicleId);

        if (!$vehicle) {
            return false;
        }

        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $vehicle->driver_profile_id === $profile->id;
    }

    /**
     * Check if user can register new vehicle
     */
    public function canRegisterVehicle(string $userId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        // Check total vehicle limit
        $maxVehicles = $this->policySettings['max_vehicles_per_driver'] ?? 2;
        $currentVehicles = $profile->vehicles()->count();

        if ($currentVehicles >= $maxVehicles) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can register vehicle of specific type
     */
    public function canRegisterVehicleType(string $userId, string $vehicleType): bool
    {
        $allowedTypes = $this->policySettings['allowed_vehicle_types'] ?? ['motorcycle', 'car'];
        if (!in_array($vehicleType, $allowedTypes)) {
            return false;
        }

        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        if(!$this->canRegisterVehicle($userId)) {
            return false;
        }

        $maxMotorcycle = $this->policySettings['max_motorcycle_per_driver'] ?? 1;
        $maxCar = $this->policySettings['max_car_per_driver'] ?? 1;

        $currentMotorcycles = $profile->vehicles()
            ->where('type', \Modules\Driver\Enums\VehicleTypeEnum::MOTORCYCLE->value)
            ->count();

        $currentCars = $profile->vehicles()
            ->where('type', \Modules\Driver\Enums\VehicleTypeEnum::CAR->value)
            ->count();

        if ($vehicleType === 'motorcycle' && $currentMotorcycles >= $maxMotorcycle) {
            return false;
        }

        if ($vehicleType === 'car' && $currentCars >= $maxCar) {
            return false;
        }

        return true;
    }

    /**
     * Get max vehicles per driver from policy settings
     */
    public function getMaxVehiclesPerDriver(): int
    {
        return $this->policySettings['max_vehicles_per_driver'] ?? 2;
    }
}
