<?php

namespace Modules\Customer\Policies\AddressManagement;

use Modules\Customer\Repositories\Address\IAddressRepository;
use Modules\Authorization\Repositories\Policy\IPolicyRepository;

class AddressManagementPolicy implements IAddressManagementPolicy
{
    private IAddressRepository $addressRepository;
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(
        IAddressRepository $addressRepository,
        IPolicyRepository $policyRepository
    ) {
        $this->addressRepository = $addressRepository;
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('customer.address.management');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'enabled' => true,
                'max_addresses_per_customer' => 5,
                'require_primary_address' => true,
                'allowed_address_types' => ['home', 'work', 'business', 'other'],
                'require_coordinates' => true,
                'coordinate_validation' => [
                    'latitude_range' => [-90, 90],
                    'longitude_range' => [-180, 180],
                ],
            ];
        }
    }

    /**
     * Check if profile can add more addresses
     */
    public function canAddAddress(string $profileId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        $currentAddressCount = $this->addressRepository->countByProfileId($profileId);
        return $currentAddressCount < $this->policySettings['max_addresses_per_customer'];
    }

    /**
     * Get maximum addresses per profile
     */
    public function getMaxAddressesPerProfile(): int
    {
        return $this->policySettings['max_addresses_per_customer'] ?? 5;
    }

    /**
     * Check if address type is allowed
     */
    public function isAddressTypeAllowed(string $addressType): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        return in_array($addressType, $this->policySettings['allowed_address_types']);
    }

    /**
     * Get allowed address types
     */
    public function getAllowedAddressTypes(): array
    {
        return $this->policySettings['allowed_address_types'] ?? ['home', 'work', 'business', 'other'];
    }

    /**
     * Check if coordinates are required
     */
    public function areCoordinatesRequired(): bool
    {
        return $this->policySettings['require_coordinates'] ?? true;
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

    /**
     * Check if primary address is required
     */
    public function isPrimaryRequired(): bool
    {
        return $this->policySettings['require_primary_address'] ?? true;
    }
}
