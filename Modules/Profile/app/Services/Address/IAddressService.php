<?php

namespace Modules\Profile\Services\Address;

use Modules\Profile\Models\Address;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Address Service
 *
 * Handles address management business logic
 */
interface IAddressService
{
    /**
     * Get all addresses for a profile
     */
    public function getProfileAddresses(string $profileId): Collection;

    /**
     * Create a new address for a profile
     */
    public function createAddress(string $profileId, array $data): Address;

    /**
     * Update an existing address
     */
    public function updateAddress(string $addressId, array $data): bool;

    /**
     * Delete an address
     */
    public function deleteAddress(string $addressId): bool;

    /**
     * Set an address as primary for the profile
     */
    public function setPrimaryAddress(string $addressId, string $profileId): bool;

    /**
     * Get the primary address for a profile
     */
    public function getPrimaryAddress(string $profileId): ?Address;

    /**
     * Validate address ownership by profile
     */
    public function validateAddressOwnership(string $addressId, string $profileId): bool;
}