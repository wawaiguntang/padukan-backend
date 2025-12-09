<?php

namespace Modules\Product\Services\AttributeMaster;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\AttributeMaster;

/**
 * Interface for Attribute Master Service
 *
 * This interface defines the contract for attribute master business operations
 * in the product module. Attribute masters are global attributes like color, size, etc.
 */
interface IAttributeMasterService
{
    /**
     * Get all attribute masters
     *
     * @return Collection The collection of all attribute masters
     */
    public function getAll(): Collection;

    /**
     * Get attribute master by ID
     *
     * @param string $id The attribute master's UUID
     * @return AttributeMaster|null The attribute master model if found, null otherwise
     */
    public function getById(string $id): ?AttributeMaster;

    /**
     * Get attribute master by key
     *
     * @param string $key The attribute master key
     * @return AttributeMaster|null The attribute master model if found, null otherwise
     */
    public function getByKey(string $key): ?AttributeMaster;

    /**
     * Create a new attribute master
     *
     * @param array $data Attribute master data containing:
     * - name: string - Attribute name
     * - key: string - Unique attribute key
     * - metadata?: json - Additional metadata (optional)
     * @return AttributeMaster The created attribute master model
     */
    public function create(array $data): AttributeMaster;

    /**
     * Update an existing attribute master
     *
     * @param string $id The attribute master's UUID
     * @param array $data Attribute master data to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete an attribute master
     *
     * @param string $id The attribute master's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Check if attribute master key exists
     *
     * @param string $key The attribute key to check
     * @param string|null $excludeId ID to exclude from check
     * @return bool True if key exists, false otherwise
     */
    public function keyExists(string $key, ?string $excludeId = null): bool;
}
