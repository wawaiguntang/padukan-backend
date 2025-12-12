<?php

namespace Modules\Tax\Repositories\TaxGroup;

use Illuminate\Database\Eloquent\Collection;
use Modules\Tax\Models\TaxGroup;

interface ITaxGroupRepository
{
    // Query Methods
    public function findAllActive(): Collection;
    public function findById(string $id): ?TaxGroup;
    public function findByIdWithDetails(string $id): ?TaxGroup;
    public function findByOwner(string $ownerId, string $ownerType): Collection;
    public function findByOwnerWithDetails(string $ownerId, string $ownerType): Collection;

    /**
     * Find tax groups by owner type (dynamic method).
     * Supports any owner type without code changes.
     *
     * @param string $ownerType Owner entity type ('system', 'merchant', 'organization', 'franchise', etc.)
     * @param string|null $ownerId Specific owner UUID, null for system/global
     * @return Collection<TaxGroup> Collection of TaxGroup models
     */
    public function findByOwnerType(string $ownerType, ?string $ownerId = null): Collection;

    /**
     * Find tax groups with their assignments loaded.
     *
     * @param string $id Tax group UUID
     * @return TaxGroup|null Tax group with assignments relationship loaded
     */
    public function findWithAssignments(string $id): ?TaxGroup;

    /**
     * Find tax groups assigned to a specific entity.
     *
     * @param string $entityType Entity type ('region', 'category', 'product', etc.)
     * @param string $entityId Entity UUID
     * @return Collection<TaxGroup> Collection of assigned tax groups
     */
    public function findByAssignedEntity(string $entityType, string $entityId): Collection;

    // Legacy methods
    /**
     * @deprecated Use findByOwnerType('system') instead
     */
    public function findSystemTaxGroups(): Collection;

    public function findByIds(array $ids): Collection;

    // Management Methods
    public function create(array $data): TaxGroup;

    /**
     * Create multiple tax groups in bulk.
     *
     * @param array<array{
     *     owner_type: string,
     *     owner_id: string|null,
     *     name: string,
     *     description: string|null,
     *     is_active: bool
     * }> $groupsData Array of tax group data
     * @return Collection<TaxGroup> Collection of created TaxGroup models
     */
    public function createBulk(array $groupsData): Collection;

    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
}
