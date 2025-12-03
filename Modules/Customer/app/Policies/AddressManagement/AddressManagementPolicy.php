<?php

namespace Modules\Customer\Policies\AddressManagement;

use Modules\Customer\Repositories\Address\IAddressRepository;
use App\Shared\Setting\Services\ISettingService;

class AddressManagementPolicy implements IAddressManagementPolicy
{
    private IAddressRepository $addressRepository;
    private ISettingService $settingService;
    private array $policySettings;

    public function __construct(
        IAddressRepository $addressRepository,
        ISettingService $settingService
    ) {
        $this->addressRepository = $addressRepository;
        $this->settingService = $settingService;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $this->policySettings = $this->settingService->getSettingByKey('customer.address.management')['value'] ?? [
            'max_addresses_per_customer' => 10,
            'validate_coordinates' => true,
        ];
    }

    /**
     * Check if profile can add more addresses
     */
    public function canAddAddress(string $profileId): bool
    {
        $currentAddressCount = $this->addressRepository->countByProfileId($profileId);
        return $currentAddressCount < $this->policySettings['max_addresses_per_customer'];
    }

    /**
     * Get maximum addresses per profile
     */
    public function getMaxAddressesPerProfile(): int
    {
        return $this->policySettings['max_addresses_per_customer'] ?? 10;
    }

    /**
     * Validate coordinate ranges
     */
    public function validateCoordinates(float $latitude, float $longitude): bool
    {
        $latRange = $this->policySettings['coordinate_validation']['latitude_range'] ?? [-90, 90];
        $lngRange = $this->policySettings['coordinate_validation']['longitude_range'] ?? [-180, 180];

        return ($latitude >= $latRange[0] && $latitude <= $latRange[1]) &&
            ($longitude >= $lngRange[0] && $longitude <= $lngRange[1]);
    }
}
