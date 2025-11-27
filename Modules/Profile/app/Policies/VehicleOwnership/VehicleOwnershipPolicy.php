<?php

namespace Modules\Profile\Policies\BusinessValidation;

use Modules\Authorization\Repositories\Policy\IPolicyRepository;
use Modules\Profile\Repositories\Driver\IDriverRepository;

class VehicleOwnershipPolicy implements IVehicleOwnershipPolicy
{
    private IPolicyRepository $policyRepository;
    private IDriverRepository $driverRepository;
    private array $policySettings;

    public function __construct(
        IPolicyRepository $policyRepository,
        IDriverRepository $driverRepository
    ) {
        $this->policyRepository = $policyRepository;
        $this->driverRepository = $driverRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('profile.vehicle_ownership');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'enabled' => true,
                'max_vehicles_per_driver' => 2,
                'require_verification' => true,
                'allowed_vehicle_types' => ['motorcycle', 'car'],
                'auto_verify_owned' => false,
            ];
        }
    }

    /**
     * Check if driver can add more vehicles
     */
    public function canAddVehicle(string $driverProfileId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        $vehicles = $this->driverRepository->getDriverVehicles($driverProfileId);
        $currentVehicleCount = $vehicles->count();

        return $currentVehicleCount < $this->policySettings['max_vehicles_per_driver'];
    }

    /**
     * Get maximum vehicles per driver
     */
    public function getMaxVehiclesPerDriver(): int
    {
        return $this->policySettings['max_vehicles_per_driver'] ?? 2;
    }

    /**
     * Check if vehicle type is allowed
     */
    public function isVehicleTypeAllowed(string $vehicleType): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        return in_array($vehicleType, $this->policySettings['allowed_vehicle_types']);
    }

    /**
     * Get allowed vehicle types
     */
    public function getAllowedVehicleTypes(): array
    {
        return $this->policySettings['allowed_vehicle_types'] ?? ['motorcycle', 'car'];
    }

    /**
     * Check if verification is required for vehicles
     */
    public function isVerificationRequired(): bool
    {
        return $this->policySettings['require_verification'] ?? true;
    }

    /**
     * Check if owned vehicles should be auto-verified
     */
    public function shouldAutoVerifyOwned(): bool
    {
        return $this->policySettings['auto_verify_owned'] ?? false;
    }
}