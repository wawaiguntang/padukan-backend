<?php

namespace Modules\Driver\Policies\DriverStatus;

use Modules\Driver\Repositories\Profile\IProfileRepository;
use App\Shared\Setting\Services\ISettingService;

class DriverStatusPolicy implements IDriverStatusPolicy
{
    private IProfileRepository $profileRepository;
    private ISettingService $settingService;
    private array $policySettings;

    public function __construct(
        IProfileRepository $profileRepository,
        ISettingService $settingService
    ) {
        $this->profileRepository = $profileRepository;
        $this->settingService = $settingService;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->settingService->getSettingByKey('driver.status.management');

        if (!empty($settings)) {
            $this->policySettings = $settings['value'] ?? [];
        } else {
            // Fallback to default consolidated settings
            $this->policySettings = [
                // Status Management
                'require_location_for_online' => true,
                'validate_service_availability' => true,

                // Service Validation
                'require_verified_vehicle' => true,
                'validate_vehicle_service_mapping' => true,
                'motorcycle_services' => ['ride', 'food', 'send', 'mart'],
                'car_services' => ['car', 'send'],
                'allow_service_switching' => true,
                'service_switch_cooldown' => 300,
                'max_motorcycle_services' => 4,
                'max_car_services' => 2,
                'allow_multiple_active_vehicles' => false,

                // Location Tracking
                'require_gps_accuracy' => 100,
                'max_location_age' => 300,
                'location_update_interval' => 30,
                'privacy_mode_enabled' => false,
                'location_history_retention' => 24,
                'geofence_enabled' => true,
                'allowed_countries' => ['ID'],

                // Rate Limiting
                'max_orders_per_hour' => 10,
                'max_orders_per_day' => 50,
                'order_acceptance_timeout' => 30,
                'auto_pause_after_rejections' => 3,
                'pause_duration' => 900,
                'rejection_rate_threshold' => 0.3,
            ];
        }
    }

    /**
     * Check if user can view their driver status
     */
    public function canViewStatus(string $userId, string $profileId): bool
    {
        $profile = $this->profileRepository->findById($profileId);

        return $profile && $profile->user_id === $userId;
    }

    /**
     * Check if user can update their online/offline status
     */
    public function canUpdateOnlineStatus(string $userId, string $profileId): bool
    {
        if (!$this->canViewStatus($userId, $profileId)) {
            return false;
        }

        return $this->canGoOnline($userId, $profileId);
    }

    /**
     * Check if user can update their operational status
     */
    public function canUpdateOperationalStatus(string $userId, string $profileId, string $newStatus): bool
    {
        if (!$this->canViewStatus($userId, $profileId)) {
            return false;
        }

        $allowedStatuses = ['available', 'on_order', 'rest'];
        return in_array($newStatus, $allowedStatuses);
    }

    /**
     * Check if user can set active service
     */
    public function canSetActiveService(string $userId, string $profileId, string $service): bool
    {
        if (!$this->canViewStatus($userId, $profileId)) {
            return false;
        }

        $profile = $this->profileRepository->findById($profileId);

        if (!$profile) {
            return false;
        }

        // Use consolidated policy settings
        if ($this->policySettings['validate_vehicle_service_mapping'] ?? true) {
            if (!$this->canUseServiceWithVehicles($userId, $profileId, $service)) {
                return false;
            }
        }

        return in_array($service, $profile->getAvailableServices());
    }

    /**
     * Check if user can update location
     */
    public function canUpdateLocation(string $userId, string $profileId): bool
    {
        if (!$this->canViewStatus($userId, $profileId)) {
            return false;
        }

        // Location tracking is enabled by default in consolidated policy
        return true;
    }

    /**
     * Check if driver can go online (has verified vehicles, etc.)
     */
    public function canGoOnline(string $userId, string $profileId): bool
    {
        $profile = $this->profileRepository->findById($profileId);

        if (!$profile || $profile->user_id !== $userId) {
            return false;
        }

        // Check if profile is verified
        if (!$profile->is_verified) {
            return false;
        }


        $hasVerifiedVehicle = $profile->vehicles()->where('is_verified', true)->exists();
        if (!$hasVerifiedVehicle) {
            return false;
        }

        // Check if driver has available services
        if (empty($profile->getAvailableServices())) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use a specific service based on their verified vehicles
     */
    public function canUseServiceWithVehicles(string $userId, string $profileId, string $service): bool
    {
        $profile = $this->profileRepository->findById($profileId);

        if (!$profile || $profile->user_id !== $userId) {
            return false;
        }

        // Use consolidated policy settings
        $requireVerifiedVehicle = $this->policySettings['require_verified_vehicle'] ?? true;

        if (!$requireVerifiedVehicle) {
            return true; // Allow if vehicle verification not required
        }

        // Get verified vehicles
        $verifiedVehicles = $profile->vehicles()->where('is_verified', true)->get();

        if ($verifiedVehicles->isEmpty()) {
            return false;
        }

        $motorcycleServices = $this->policySettings['motorcycle_services'] ?? [];
        $carServices = $this->policySettings['car_services'] ?? [];

        // Check if service is allowed for any of the driver's verified vehicles
        foreach ($verifiedVehicles as $vehicle) {
            $vehicleType = $vehicle->type->value; // Assuming enum value

            if ($vehicleType === 'motorcycle' && in_array($service, $motorcycleServices)) {
                return true;
            }

            if ($vehicleType === 'car' && in_array($service, $carServices)) {
                return true;
            }
        }

        return false;
    }
}
