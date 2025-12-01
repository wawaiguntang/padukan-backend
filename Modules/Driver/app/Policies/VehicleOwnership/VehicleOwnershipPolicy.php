<?php

namespace Modules\Driver\Policies\VehicleOwnership;

use Modules\Driver\Repositories\Vehicle\IVehicleRepository;
use Modules\Driver\Repositories\Profile\IProfileRepository;
use App\Shared\Authorization\Repositories\IPolicyRepository;

class VehicleOwnershipPolicy implements IVehicleOwnershipPolicy
{
    private IVehicleRepository $vehicleRepository;
    private IProfileRepository $profileRepository;
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(
        IVehicleRepository $vehicleRepository,
        IProfileRepository $profileRepository,
        IPolicyRepository $policyRepository
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->profileRepository = $profileRepository;
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('driver.vehicle.ownership');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'strict_ownership' => true,
                'check_user_active' => true,
            ];
        }
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

        // Check if vehicle belongs to user's profile
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $vehicle->driver_profile_id === $profile->id;
    }

    /**
     * Check if user can access vehicle data
     */
    public function canAccessVehicle(string $userId, string $vehicleId): bool
    {
        return $this->ownsVehicle($userId, $vehicleId);
    }

    /**
     * Check if user can modify vehicle data
     */
    public function canModifyVehicle(string $userId, string $vehicleId): bool
    {
        return $this->ownsVehicle($userId, $vehicleId);
    }

    /**
     * Check if user can delete vehicle
     */
    public function canDeleteVehicle(string $userId, string $vehicleId): bool
    {
        return $this->ownsVehicle($userId, $vehicleId);
    }

    /**
     * Check if user can register new vehicle
     */
    public function canRegisterVehicle(string $userId): bool
    {
        // Get vehicle management policy settings
        $vehicleManagementSettings = $this->policyRepository->getSetting('driver.vehicle.management');

        if (!$vehicleManagementSettings) {
            return true; // Allow if no policy settings
        }

        // Check vehicle count limit
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        // Check total vehicle limit
        $maxVehicles = $vehicleManagementSettings['max_vehicles_per_driver'] ?? 3;
        $currentVehicles = $profile->vehicles()->count();

        if ($currentVehicles >= $maxVehicles) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can submit vehicle verification
     */
    public function canSubmitVehicleVerification(string $userId, string $vehicleId): bool
    {
        if (!$this->canModifyVehicle($userId, $vehicleId)) {
            return false;
        }

        $vehicle = $this->vehicleRepository->findById($vehicleId);

        if (!$vehicle) {
            return false;
        }

        // Can submit if not verified and not already pending
        return $vehicle->verification_status === null ||
            !in_array($vehicle->verification_status, ['pending', 'verified']);
    }

    /**
     * Check if user can resubmit vehicle verification
     */
    public function canResubmitVehicleVerification(string $userId, string $vehicleId): bool
    {
        if (!$this->canModifyVehicle($userId, $vehicleId)) {
            return false;
        }

        $vehicle = $this->vehicleRepository->findById($vehicleId);

        if (!$vehicle) {
            return false;
        }

        // Can resubmit only if rejected
        return $vehicle->verification_status->value === 'rejected';
    }

    /**
     * Check if user can register vehicle of specific type
     */
    public function canRegisterVehicleType(string $userId, string $vehicleType): bool
    {
        // Get vehicle management policy settings
        $vehicleManagementSettings = $this->policyRepository->getSetting('driver.vehicle.management');

        if (!$vehicleManagementSettings) {
            return true; // Allow if no policy settings
        }

        // Check if vehicle type is allowed
        $allowedTypes = $vehicleManagementSettings['allowed_vehicle_types'] ?? ['motorcycle', 'car'];
        if (!in_array($vehicleType, $allowedTypes)) {
            return false;
        }

        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        // Check total vehicle limit first
        $maxVehicles = $vehicleManagementSettings['max_vehicles_per_driver'] ?? 3;
        $currentVehicles = $profile->vehicles()->count();

        if ($currentVehicles >= $maxVehicles) {
            return false;
        }

        // Check specific vehicle type limits
        $maxMotorcycle = $vehicleManagementSettings['max_motorcycle_per_driver'] ?? 1;
        $maxCar = $vehicleManagementSettings['max_car_per_driver'] ?? 1;

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
        $vehicleManagementSettings = $this->policyRepository->getSetting('driver.vehicle.management');
        return $vehicleManagementSettings['max_vehicles_per_driver'] ?? 3;
    }
}
