<?php

namespace Modules\Product\Services\AttributeCustom;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\AttributeCustom;

/**
 * Interface for Attribute Custom Service
 *
 * This interface defines the contract for attribute custom business operations
 * in the product module. Attribute customs are merchant-specific attributes.
 */
interface IAttributeCustomService
{
    /**
     * Get all attribute customs for a merchant
     *
     * @param string $merchantId The merchant UUID
     * @return Collection The collection of attribute customs
     */
    public function getAllByMerchant(string $merchantId): Collection;

    /**
     * Get attribute custom by ID
     *
     * @param string $id The attribute custom's UUID
     * @param string $merchantId The merchant UUID for ownership validation
     * @return AttributeCustom|null The attribute custom model if found, null otherwise
     */
    public function getById(string $id, string $merchantId): ?AttributeCustom;

    /**
     * Get attribute custom by key for a merchant
     *
     * @param string $key The attribute key
     * @param string $merchantId The merchant UUID
     * @return AttributeCustom|null The attribute custom model if found, null otherwise
     */
    public function getByKey(string $key, string $merchantId): ?AttributeCustom;

    /**
     * Create a new attribute custom for a merchant
     *
     * @param array $data Attribute custom data containing:
     * - name: string - Attribute name
     * - key: string - Unique attribute key for the merchant
     * - metadata?: json - Additional metadata (optional)
     * @param string $merchantId The merchant UUID
     * @return AttributeCustom The created attribute custom model
     */
    public function create(array $data, string $merchantId): AttributeCustom;

    /**
     * Update an existing attribute custom
     *
     * @param string $id The attribute custom's UUID
     * @param array $data Attribute custom data to update
     * @param string $merchantId The merchant UUID for ownership validation
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data, string $merchantId): bool;

    /**
     * Delete an attribute custom
     *
     * @param string $id The attribute custom's UUID
     * @param string $merchantId The merchant UUID for ownership validation
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id, string $merchantId): bool;

    /**
     * Check if attribute custom key exists for a merchant
     *
     * @param string $key The attribute key to check
     * @param string $merchantId The merchant UUID
     * @param string|null $excludeId ID to exclude from check
     * @return bool True if key exists, false otherwise
     */
    public function keyExists(string $key, string $merchantId, ?string $excludeId = null): bool;
}
