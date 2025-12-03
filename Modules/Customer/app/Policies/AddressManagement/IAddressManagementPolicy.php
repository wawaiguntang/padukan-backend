<?php

namespace Modules\Customer\Policies\AddressManagement;

interface IAddressManagementPolicy
{
    /**
     * Check if profile can add more addresses
     */
    public function canAddAddress(string $profileId): bool;

    /**
     * Get maximum addresses per profile
     */
    public function getMaxAddressesPerProfile(): int;

    /**
     * Validate coordinate ranges
     */
    public function validateCoordinates(float $latitude, float $longitude): bool;
}
