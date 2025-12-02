<?php

namespace Modules\Customer\Services\Address;

use Modules\Customer\Models\Address;
use Modules\Customer\Models\Profile;
use Illuminate\Database\Eloquent\Collection;

/**
 * Address Service Interface
 *
 * Defines contract for address business logic operations
 */
interface IAddressService
{
    /**
     * Create a new address for a profile
     *
     * @param string $profileId
     * @param array $data
     * @return Address
     */
    public function createAddress(string $profileId, array $data): Address;

    /**
     * Get all addresses for a profile
     *
     * @param string $profileId
     * @return Collection
     */
    public function getAddressesByProfileId(string $profileId): Collection;

    /**
     * Get a specific address by ID
     *
     * @param string $id
     * @return Address|null
     */
    public function getAddressById(string $id): ?Address;

    /**
     * Update an existing address
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function updateAddress(string $id, array $data): bool;

    /**
     * Delete an address
     *
     * @param string $id
     * @return bool
     */
    public function deleteAddress(string $id): bool;

    /**
     * Set address as primary for its profile
     *
     * @param string $id
     * @return bool
     */
    public function setAsPrimary(string $id): bool;

    /**
     * Check if user owns the address
     *
     * @param string $addressId
     * @param string $userId
     * @return bool
     */
    public function isAddressOwnedByUser(string $addressId, string $userId): bool;

    /**
     * Get or create profile for user
     *
     * @param string $userId
     * @param array $defaultData
     * @return Profile
     */
    public function getOrCreateProfile(string $userId, array $defaultData = []): Profile;
}
