<?php

namespace Modules\Tax\Repositories\Tax;

use Illuminate\Database\Eloquent\Collection;
use Modules\Tax\Models\Tax;

interface ITaxRepository
{
    /**
     * Find all active taxes across all owner types.
     *
     * @return Collection<Tax> Collection of active Tax models
     */
    public function findAllActive(): Collection;

    /**
     * Find a tax by its UUID.
     *
     * @param string $id Tax UUID
     * @return Tax|null Tax model or null if not found
     */
    public function findById(string $id): ?Tax;

    /**
     * Find a tax by its unique slug.
     *
     * @param string $slug Tax slug (e.g., 'ppn', 'service-charge')
     * @return Tax|null Tax model or null if not found
     */
    public function findBySlug(string $slug): ?Tax;

    /**
     * Find all taxes that belong to a specific tax group.
     *
     * @param string $groupId Tax group UUID
     * @return Collection<Tax> Collection of Tax models in the group
     */
    public function findByGroup(string $groupId): Collection;

    /**
     * Find taxes by specific owner (legacy method).
     *
     * @param string $ownerId Owner entity UUID
     * @param string $ownerType Owner entity type ('merchant', 'organization', etc.)
     * @return Collection<Tax> Collection of Tax models owned by the entity
     */
    public function findByOwner(string $ownerId, string $ownerType): Collection;

    /**
     * Find taxes by owner type (dynamic method).
     * Supports any owner type without code changes.
     *
     * @param string $ownerType Owner entity type ('system', 'merchant', 'organization', 'franchise', etc.)
     * @param string|null $ownerId Specific owner UUID, null for global/system taxes
     * @return Collection<Tax> Collection of Tax models
     *
     * @example
     * // Find all system/global taxes
     * $systemTaxes = $repo->findByOwnerType('system');
     *
     * // Find taxes for specific merchant
     * $merchantTaxes = $repo->findByOwnerType('merchant', $merchantId);
     *
     * // Find taxes for future franchise entity
     * $franchiseTaxes = $repo->findByOwnerType('franchise', $franchiseId);
     */
    public function findByOwnerType(string $ownerType, ?string $ownerId = null): Collection;

    /**
     * Find taxes by multiple tax group IDs.
     *
     * @param array<string> $groupIds Array of tax group UUIDs
     * @return Collection<Tax> Collection of Tax models
     */
    public function findByGroupIds(array $groupIds): Collection;

    /**
     * Find all system/global taxes (legacy method).
     *
     * @return Collection<Tax> Collection of system Tax models
     * @deprecated Use findByOwnerType('system') instead
     */
    public function findSystemTaxes(): Collection;

    /**
     * Find taxes for a specific merchant (legacy method).
     *
     * @param string $merchantId Merchant UUID
     * @return Collection<Tax> Collection of merchant Tax models
     * @deprecated Use findByOwnerType('merchant', $merchantId) instead
     */
    public function findMerchantTaxes(string $merchantId): Collection;

    /**
     * Create a new tax.
     *
     * @param array{
     *     owner_type: string,
     *     owner_id: string|null,
     *     name: string,
     *     slug: string,
     *     description: string|null,
     *     is_active: bool
     * } $data Tax creation data
     * @return Tax Created Tax model
     */
    public function create(array $data): Tax;

    /**
     * Create multiple taxes in bulk.
     *
     * @param array<array{
     *     owner_type: string,
     *     owner_id: string|null,
     *     name: string,
     *     slug: string,
     *     description: string|null,
     *     is_active: bool
     * }> $taxesData Array of tax data arrays
     * @return Collection<Tax> Collection of created Tax models
     */
    public function createBulk(array $taxesData): Collection;

    /**
     * Update an existing tax.
     *
     * @param string $id Tax UUID
     * @param array{
     *     name?: string,
     *     slug?: string,
     *     description?: string|null,
     *     is_active?: bool,
     *     owner_type?: string,
     *     owner_id?: string|null
     * } $data Updated tax data (partial update supported)
     * @return bool True if update successful
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a tax by its UUID.
     *
     * @param string $id Tax UUID
     * @return bool True if deletion successful
     */
    public function delete(string $id): bool;
}
