<?php

namespace Modules\Driver\Policies\ServiceValidation;

use Modules\Driver\Repositories\Profile\IProfileRepository;
use Modules\Authorization\Repositories\Policy\IPolicyRepository;

class ServiceValidationPolicy implements IServiceValidationPolicy
{
    private IProfileRepository $profileRepository;
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(
        IProfileRepository $profileRepository,
        IPolicyRepository $policyRepository
    ) {
        $this->profileRepository = $profileRepository;
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('driver.service.validation');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'require_verified_vehicle' => true,
                'validate_vehicle_service_mapping' => true,
                'motorcycle_services' => ['ride', 'food', 'send', 'mart'],
                'car_services' => ['car', 'send'],
                'allow_service_switching' => true,
            ];
        }
    }

    /**
     * Check if driver can provide a specific service based on verified vehicles
     */
    public function canProvideService(string $userId, string $profileId, string $service): bool
    {
        $profile = $this->profileRepository->findById($profileId);

        if (!$profile || $profile->user_id !== $userId) {
            return false;
        }

        $availableServices = $this->getAvailableServices($userId, $profileId);

        return in_array($service, $availableServices);
    }

    /**
     * Get all services a driver can provide
     */
    public function getAvailableServices(string $userId, string $profileId): array
    {
        $profile = $this->profileRepository->findById($profileId);

        if (!$profile || $profile->user_id !== $userId) {
            return [];
        }

        return $profile->getAvailableServices();
    }

    /**
     * Validate service assignment for driver
     */
    public function validateServiceAssignment(string $userId, string $profileId, string $service): bool
    {
        // Check if service is in allowed list
        $allAllowedServices = array_merge(
            $this->policySettings['motorcycle_services'],
            $this->policySettings['car_services']
        );

        if (!in_array($service, $allAllowedServices)) {
            return false;
        }

        // Check if driver can provide this service
        return $this->canProvideService($userId, $profileId, $service);
    }

    /**
     * Check if driver meets service requirements
     */
    public function meetsServiceRequirements(string $userId, string $profileId, string $service): bool
    {
        $profile = $this->profileRepository->findById($profileId);

        if (!$profile || $profile->user_id !== $userId) {
            return false;
        }

        // Check if profile is verified
        if (!$profile->is_verified) {
            return false;
        }

        // Check if driver has verified vehicles that can provide this service
        $verifiedVehicles = $profile->vehicles()->where('is_verified', true)->get();

        foreach ($verifiedVehicles as $vehicle) {
            $vehicleServices = $this->getServicesForVehicleType($vehicle->type->value);

            if (in_array($service, $vehicleServices)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get services available for a specific vehicle type
     */
    private function getServicesForVehicleType(string $vehicleType): array
    {
        switch ($vehicleType) {
            case 'motorcycle':
                return $this->policySettings['motorcycle_services'];
            case 'car':
                return $this->policySettings['car_services'];
            default:
                return [];
        }
    }
}
