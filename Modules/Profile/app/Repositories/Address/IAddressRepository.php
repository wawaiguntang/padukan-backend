<?php

namespace Modules\Profile\Repositories\Address;

use Modules\Profile\Models\Address;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Address Repository
 */
interface IAddressRepository
{
    /**
     * Find address by ID
     */
    public function findById(string $id): ?Address;

    /**
     * Get addresses by profile ID
     */
    public function getByProfileId(string $profileId): Collection;

    /**
     * Create new address
     */
    public function create(array $data): Address;

    /**
     * Update address
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete address
     */
    public function delete(string $id): bool;

    /**
     * Set address as primary
     */
    public function setAsPrimary(string $id, string $profileId): bool;

    /**
     * Get primary address for profile
     */
    public function getPrimaryAddress(string $profileId): ?Address;
}