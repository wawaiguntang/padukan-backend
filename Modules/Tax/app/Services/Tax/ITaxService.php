<?php

namespace Modules\Tax\Services\Tax;

use Illuminate\Database\Eloquent\Collection;
use Modules\Tax\Models\Tax;
use Modules\Tax\Models\TaxGroup;
use Modules\Tax\Models\TaxRate;

interface ITaxService
{
    /**
     * Create a new tax for any owner type.
     * Supports dynamic owner types without code changes.
     *
     * @param string $ownerType Owner entity type ('system', 'organization', 'merchant', 'franchise', etc.)
     * @param string|null $ownerId Specific owner UUID, null for system/global taxes
     * @param array{
     *     name: string,
     *     slug: string,
     *     description: string|null,
     *     is_active: bool
     * } $data Tax creation data
     * @return Tax Created Tax model
     *
     * @example
     * // Create system tax
     * $tax = $service->createTax('system', null, [
     *     'name' => 'PPN',
     *     'slug' => 'ppn',
     *     'description' => 'Pajak Pertambahan Nilai',
     *     'is_active' => true
     * ]);
     *
     * // Create merchant tax
     * $tax = $service->createTax('merchant', $merchantId, [
     *     'name' => 'Service Charge',
     *     'slug' => 'service-charge',
     *     'is_active' => true
     * ]);
     */
    public function createTax(string $ownerType, ?string $ownerId, array $data): Tax;

    /**
     * Update an existing tax with ownership validation.
     *
     * @param string $ownerType Owner entity type for validation
     * @param string|null $ownerId Owner UUID for validation
     * @param string $taxId Tax UUID to update
     * @param array{
     *     name?: string,
     *     slug?: string,
     *     description?: string|null,
     *     is_active?: bool
     * } $data Updated tax data (partial update supported)
     * @return bool True if update successful
     *
     * @throws \Exception If tax ownership validation fails
     */
    public function updateTax(string $ownerType, ?string $ownerId, string $taxId, array $data): bool;

    /**
     * Delete a tax with ownership validation.
     *
     * @param string $ownerType Owner entity type for validation
     * @param string|null $ownerId Owner UUID for validation
     * @param string $taxId Tax UUID to delete
     * @return bool True if deletion successful
     *
     * @throws \Exception If tax ownership validation fails
     */
    public function deleteTax(string $ownerType, ?string $ownerId, string $taxId): bool;

    /**
     * Get taxes by owner type (dynamic query).
     * Supports any owner type without code changes.
     *
     * @param string $ownerType Owner entity type
     * @param string|null $ownerId Specific owner UUID, null for all of owner type
     * @return Collection<Tax> Collection of Tax models
     *
     * @example
     * // Get all system taxes
     * $systemTaxes = $service->getTaxes('system');
     *
     * // Get taxes for specific merchant
     * $merchantTaxes = $service->getTaxes('merchant', $merchantId);
     */
    public function getTaxes(string $ownerType, ?string $ownerId = null): Collection;

    /**
     * Create a tax group for any owner type.
     *
     * @param string $ownerType Owner entity type
     * @param string|null $ownerId Owner UUID
     * @param array{
     *     name: string,
     *     description: string|null,
     *     is_active: bool
     * } $data Tax group creation data
     * @return TaxGroup Created TaxGroup model
     */
    public function createTaxGroup(string $ownerType, ?string $ownerId, array $data): TaxGroup;

    /**
     * Assign a tax group to context entities (dynamic assignment).
     * Context can contain any entity types without code changes.
     *
     * @param string $taxGroupId Tax group UUID to assign
     * @param array<string, array<string>> $context Context assignment data where:
     *   - Key: plural entity type ('regions', 'categories', 'products', 'branches', etc.)
     *   - Value: array of entity UUIDs
     * @return bool True if assignment successful
     *
     * @example
     * // Assign to regions and categories
     * $service->assignTaxToContext($taxGroupId, [
     *     'regions' => ['region-1', 'region-2'],
     *     'categories' => ['category-1', 'category-2']
     * ]);
     *
     * // Future: assign to branches and outlets
     * $service->assignTaxToContext($taxGroupId, [
     *     'branches' => ['branch-1', 'branch-2'],
     *     'outlets' => ['outlet-1', 'outlet-2']
     * ]);
     */
    public function assignTaxToContext(string $taxGroupId, array $context): bool;

    /**
     * Calculate tax for a given price with owner context.
     * Automatically resolves tax hierarchy (System → Organization → Specific Owner).
     *
     * @param float $price Base price to calculate tax for
     * @param string $ownerType Owner entity type
     * @param string|null $ownerId Specific owner UUID
     * @param array<string, array<string>> $context Transaction context (regions, categories, etc.)
     * @return array{
     *     base_amount: float,
     *     taxes: array<array{
     *         name: string,
     *         rate: float,
     *         amount: float,
     *         priority: int
     *     }>,
     *     total_tax: float,
     *     grand_total: float
     * } Tax calculation result
     *
     * @example
     * $result = $service->calculateTaxForOwner(100000, 'merchant', $merchantId, [
     *     'regions' => ['jakarta'],
     *     'categories' => ['food']
     * ]);
     *
     * // Result: ['base_amount' => 100000, 'total_tax' => 15000, 'grand_total' => 115000, ...]
     */
    public function calculateTaxForOwner(float $price, string $ownerType, ?string $ownerId, array $context = []): array;

    /**
     * Check if user can manage a specific tax.
     * Dynamic permission check based on user type and ownership hierarchy.
     *
     * @param string $userId User UUID performing the action
     * @param string $userType User type ('super_admin', 'organization_admin', 'merchant', etc.)
     * @param string $ownerType Tax owner entity type
     * @param string|null $ownerId Tax owner UUID
     * @param string $taxId Tax UUID being managed
     * @return bool True if user has management permission
     *
     * @example
     * // Super admin can manage all taxes
     * $canManage = $service->canManageTax($userId, 'super_admin', 'system', null, $taxId);
     *
     * // Merchant can only manage their own taxes
     * $canManage = $service->canManageTax($userId, 'merchant', 'merchant', $merchantId, $taxId);
     */
    public function canManageTax(string $userId, string $userType, string $ownerType, ?string $ownerId, string $taxId): bool;

    /**
     * Validate tax ownership dynamically.
     *
     * @param string $taxId Tax UUID to validate
     * @param string|null $ownerId Expected owner UUID
     * @param string $ownerType Expected owner type
     * @return bool True if ownership is valid
     *
     * @throws \Exception If ownership validation fails
     */
    public function validateTaxOwnership(string $taxId, ?string $ownerId, string $ownerType): bool;

    // ===== TAX GROUP MANAGEMENT =====

    /**
     * Update an existing tax group with ownership validation.
     *
     * @param string $ownerType Owner entity type for validation
     * @param string|null $ownerId Owner UUID for validation
     * @param string $groupId Tax group UUID to update
     * @param array{
     *     name?: string,
     *     description?: string|null,
     *     is_active?: bool
     * } $data Updated tax group data (partial update supported)
     * @return bool True if update successful
     *
     * @throws \Exception If tax group ownership validation fails
     */
    public function updateTaxGroup(string $ownerType, ?string $ownerId, string $groupId, array $data): bool;

    /**
     * Delete a tax group with ownership validation.
     *
     * @param string $ownerType Owner entity type for validation
     * @param string|null $ownerId Owner UUID for validation
     * @param string $groupId Tax group UUID to delete
     * @return bool True if deletion successful
     *
     * @throws \Exception If tax group ownership validation fails
     */
    public function deleteTaxGroup(string $ownerType, ?string $ownerId, string $groupId): bool;

    /**
     * Get tax groups by owner type (dynamic query).
     * Supports any owner type without code changes.
     *
     * @param string $ownerType Owner entity type
     * @param string|null $ownerId Specific owner UUID, null for all of owner type
     * @return Collection<TaxGroup> Collection of TaxGroup models
     *
     * @example
     * // Get all system tax groups
     * $systemGroups = $service->getTaxGroups('system');
     *
     * // Get tax groups for specific merchant
     * $merchantGroups = $service->getTaxGroups('merchant', $merchantId);
     */
    public function getTaxGroups(string $ownerType, ?string $ownerId = null): Collection;

    /**
     * Get tax group by ID with ownership validation.
     *
     * @param string $groupId Tax group UUID
     * @return TaxGroup|null Tax group model or null if not found
     */
    public function getTaxGroupById(string $groupId): ?TaxGroup;

    // ===== TAX RATE MANAGEMENT =====

    /**
     * Create a new tax rate for a tax group.
     *
     * @param string $groupId Tax group UUID
     * @param array{
     *     tax_id: string,
     *     rate: float,
     *     type: string,
     *     is_inclusive: bool,
     *     priority: int,
     *     based_on: string|null,
     *     valid_from: string|null,
     *     valid_until: string|null,
     *     min_price: float|null,
     *     max_price: float|null
     * } $data Tax rate creation data
     * @return TaxRate Created TaxRate model
     */
    public function createTaxRate(string $groupId, array $data): TaxRate;

    /**
     * Update an existing tax rate.
     *
     * @param string $rateId Tax rate UUID
     * @param array{
     *     rate?: float,
     *     type?: string,
     *     is_inclusive?: bool,
     *     priority?: int,
     *     based_on?: string|null,
     *     valid_from?: string|null,
     *     valid_until?: string|null,
     *     min_price?: float|null,
     *     max_price?: float|null
     * } $data Updated tax rate data (partial update supported)
     * @return bool True if update successful
     */
    public function updateTaxRate(string $rateId, array $data): bool;

    /**
     * Delete a tax rate.
     *
     * @param string $rateId Tax rate UUID
     * @return bool True if deletion successful
     */
    public function deleteTaxRate(string $rateId): bool;

    /**
     * Get tax rates by group ID.
     *
     * @param string $groupId Tax group UUID
     * @return Collection<TaxRate> Collection of TaxRate models
     */
    public function getTaxRatesByGroup(string $groupId): Collection;

    /**
     * Get active tax rates by group ID (considering validity dates).
     *
     * @param string $groupId Tax group UUID
     * @return Collection<TaxRate> Collection of active TaxRate models
     */
    public function getActiveTaxRatesByGroup(string $groupId): Collection;

    // ===== ADVANCED ASSIGNMENT MANAGEMENT =====

    /**
     * Remove tax assignments from context entities.
     * Opposite of assignTaxToContext.
     *
     * @param string $taxGroupId Tax group UUID to remove from
     * @param array<string, array<string>> $context Context entities to remove assignments from
     * @return bool True if removal successful
     *
     * @example
     * // Remove from specific regions and categories
     * $service->removeTaxFromContext($taxGroupId, [
     *     'regions' => ['region-1', 'region-2'],
     *     'categories' => ['category-1']
     * ]);
     */
    public function removeTaxFromContext(string $taxGroupId, array $context): bool;

    /**
     * Get all entities assigned to a tax group.
     *
     * @param string $taxGroupId Tax group UUID
     * @return Collection Collection of assigned entities with types
     */
    public function getAssignedEntities(string $taxGroupId): Collection;

    /**
     * Get all tax groups assigned to a specific entity.
     *
     * @param string $entityType Entity type ('region', 'category', 'product', etc.)
     * @param string $entityId Entity UUID
     * @return Collection<TaxGroup> Collection of assigned tax groups
     */
    public function getTaxGroupsForEntity(string $entityType, string $entityId): Collection;

    // ===== BULK OPERATIONS =====

    /**
     * Create multiple taxes in bulk for any owner type.
     *
     * @param string $ownerType Owner entity type
     * @param string|null $ownerId Owner UUID
     * @param array<array{
     *     name: string,
     *     slug: string,
     *     description: string|null,
     *     is_active: bool
     * }> $taxesData Array of tax data arrays
     * @return Collection<Tax> Collection of created Tax models
     */
    public function createBulkTaxes(string $ownerType, ?string $ownerId, array $taxesData): Collection;

    /**
     * Update multiple tax groups in bulk.
     *
     * @param array<array{
     *     id: string,
     *     name?: string,
     *     description?: string|null,
     *     is_active?: bool
     * }> $groupsData Array of tax group update data
     * @return bool True if all updates successful
     */
    public function updateBulkTaxGroups(array $groupsData): bool;

    /**
     * Delete multiple taxes in bulk with ownership validation.
     *
     * @param string $ownerType Owner entity type for validation
     * @param string|null $ownerId Owner UUID for validation
     * @param array<string> $taxIds Array of tax UUIDs to delete
     * @return bool True if all deletions successful
     */
    public function deleteBulkTaxes(string $ownerType, ?string $ownerId, array $taxIds): bool;

    // ===== ADVANCED QUERYING & REPORTING =====

    /**
     * Get all taxes by owner type (admin function).
     * Returns all taxes for the specified owner type regardless of specific owner.
     *
     * @param string $ownerType Owner entity type
     * @return Collection<Tax> Collection of all Tax models for the owner type
     */
    public function getAllTaxesByOwnerType(string $ownerType): Collection;

    /**
     * Get tax statistics for an owner.
     *
     * @param string $ownerType Owner entity type
     * @param string|null $ownerId Specific owner UUID
     * @return array{
     *     total_taxes: int,
     *     active_taxes: int,
     *     total_groups: int,
     *     active_groups: int,
     *     total_rates: int,
     *     active_rates: int
     * } Tax statistics
     */
    public function getTaxStatistics(string $ownerType, ?string $ownerId = null): array;

    /**
     * Get tax hierarchy for an owner (admin function).
     * Shows complete tax structure with groups and rates.
     *
     * @param string $ownerType Owner entity type
     * @param string|null $ownerId Specific owner UUID
     * @return array Complete tax hierarchy structure
     */
    public function getTaxHierarchy(string $ownerType, ?string $ownerId = null): array;

    /**
     * Search taxes by query string.
     *
     * @param string $query Search query
     * @param string $ownerType Owner entity type for scoping
     * @param string|null $ownerId Specific owner UUID for scoping
     * @return Collection<Tax> Collection of matching Tax models
     */
    public function searchTaxes(string $query, string $ownerType, ?string $ownerId = null): Collection;

    // ===== PERMISSION MANAGEMENT =====

    /**
     * Check if user can manage a specific tax group.
     * Dynamic permission check based on user type and ownership hierarchy.
     *
     * @param string $userId User UUID performing the action
     * @param string $userType User type ('super_admin', 'organization_admin', 'merchant', etc.)
     * @param string $ownerType Tax group owner entity type
     * @param string|null $ownerId Tax group owner UUID
     * @param string $groupId Tax group UUID being managed
     * @return bool True if user has management permission
     */
    public function canManageTaxGroup(string $userId, string $userType, string $ownerType, ?string $ownerId, string $groupId): bool;

    /**
     * Check if user can manage tax assignments.
     * Dynamic permission check for assignment operations.
     *
     * @param string $userId User UUID performing the action
     * @param string $userType User type ('super_admin', 'organization_admin', 'merchant', etc.)
     * @param string $taxGroupId Tax group UUID being assigned
     * @return bool True if user has assignment management permission
     */
    public function canManageTaxAssignment(string $userId, string $userType, string $taxGroupId): bool;

    /**
     * Validate tax group ownership dynamically.
     *
     * @param string $groupId Tax group UUID to validate
     * @param string|null $ownerId Expected owner UUID
     * @param string $ownerType Expected owner type
     * @return bool True if ownership is valid
     */
    public function validateTaxGroupOwnership(string $groupId, ?string $ownerId, string $ownerType): bool;
}
