<?php

namespace Modules\Customer\Repositories\Address;

use Illuminate\Database\Eloquent\Collection;
use Modules\Customer\Enums\AddressTypeEnum;
use Modules\Customer\Models\Address;

/**
 * Interface for Address Repository
 *
 * This interface defines the contract for address data operations
 * in the customer module.
 */
interface IAddressRepository
{
    /**
     * Find addresses by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return Collection<Address> Collection of address models
     */
    public function findByProfileId(string $profileId): Collection;

    /**
     * Find a address by ID
     *
     * @param string $id The address's UUID
     * @return Address|null The address model if found, null otherwise
     */
    public function findById(string $id): ?Address;

    /**
     * Create a new address
     *
     * @param array $data Address data containing:
     * - profile_id: string - Profile's UUID
     * - type: AddressTypeEnum - Address type
     * - label: string - Address label
     * - street: string - Street address
     * - city: string - City
     * - province: string - Province/State
     * - postal_code: string - Postal code
     * - latitude?: float - Latitude coordinate (optional)
     * - longitude?: float - Longitude coordinate (optional)
     * - is_primary?: bool - Whether this is the primary address (optional)
     * @return Address The created address model
     */
    public function create(array $data): Address;

    /**
     * Update an existing address
     *
     * @param string $id The address's UUID
     * @param array $data Address data to update (same structure as create)
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete an address
     *
     * @param string $id The address's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Set an address as primary for a profile
     *
     * @param string $id The address's UUID
     * @return bool True if update was successful, false otherwise
     */
    public function setAsPrimary(string $id): bool;

    /**
     * Find primary address by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return Address|null The primary address model if found, null otherwise
     */
    public function findPrimaryByProfileId(string $profileId): ?Address;

    /**
     * Find addresses by type and profile ID
     *
     * @param string $profileId The profile's UUID
     * @param AddressTypeEnum $type The address type
     * @return Collection<Address> Collection of address models
     */
    public function findByTypeAndProfileId(string $profileId, AddressTypeEnum $type): Collection;

    /**
     * Check if address exists by ID
     *
     * @param string $id The address's UUID
     * @return bool True if address exists, false otherwise
     */
    public function existsById(string $id): bool;

    /**
     * Count addresses by profile ID
     *
     * @param string $profileId The profile's UUID
     * @return int Number of addresses for the profile
     */
    public function countByProfileId(string $profileId): int;
}
