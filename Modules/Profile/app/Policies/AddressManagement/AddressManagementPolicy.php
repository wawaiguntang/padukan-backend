<?php

namespace Modules\Profile\Policies\AddressManagement;

use Modules\Authorization\Repositories\Policy\IPolicyRepository;
use Modules\Profile\Policies\AddressManagement\IAddressManagementPolicy;

class AddressManagementPolicy implements IAddressManagementPolicy
{
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(IPolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('profile.address_management');
        $this->policySettings = $settings ?: [
            'enabled' => true,
            'max_addresses_per_profile' => 5,
            'require_primary_address' => true,
            'allowed_address_types' => ['home', 'work', 'business', 'other'],
            'require_coordinates' => true,
            'coordinate_validation' => ['latitude_range' => [-90, 90], 'longitude_range' => [-180, 180]],
        ];
    }

    public function canAddAddress(string $profileId): bool
    {
        if (!$this->policySettings['enabled']) return true;
        // Simplified - would check current count in real implementation
        return true;
    }

    public function getMaxAddressesPerProfile(): int
    {
        return $this->policySettings['max_addresses_per_profile'] ?? 5;
    }

    public function isAddressTypeAllowed(string $addressType): bool
    {
        if (!$this->policySettings['enabled']) return true;
        return in_array($addressType, $this->policySettings['allowed_address_types']);
    }

    public function getAllowedAddressTypes(): array
    {
        return $this->policySettings['allowed_address_types'] ?? ['home', 'work', 'business', 'other'];
    }

    public function areCoordinatesRequired(): bool
    {
        return $this->policySettings['require_coordinates'] ?? true;
    }

    public function validateCoordinates(float $latitude, float $longitude): bool
    {
        $latRange = $this->policySettings['coordinate_validation']['latitude_range'] ?? [-90, 90];
        $lngRange = $this->policySettings['coordinate_validation']['longitude_range'] ?? [-180, 180];

        return $latitude >= $latRange[0] && $latitude <= $latRange[1] &&
               $longitude >= $lngRange[0] && $longitude <= $lngRange[1];
    }

    public function isPrimaryRequired(): bool
    {
        return $this->policySettings['require_primary_address'] ?? true;
    }
}