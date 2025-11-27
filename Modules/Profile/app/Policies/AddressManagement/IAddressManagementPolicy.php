<?php

namespace Modules\Profile\Policies\AddressManagement;

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
     * Check if address type is allowed
     */
    public function isAddressTypeAllowed(string $addressType): bool;

    /**
     * Get allowed address types
     */
    public function getAllowedAddressTypes(): array;

    /**
     * Check if coordinates are required
     */
    public function areCoordinatesRequired(): bool;

    /**
     * Validate coordinate ranges
     */
    public function validateCoordinates(float $latitude, float $longitude): bool;

    /**
     * Check if primary address is required
     */
    public function isPrimaryRequired(): bool;
}