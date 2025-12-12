<?php

namespace Modules\Tax\Repositories\TaxAssignment;

use Illuminate\Database\Eloquent\Collection;
use Modules\Tax\Models\TaxAssignment;

interface ITaxAssignmentRepository
{
    /**
     * Find all assignments for a specific tax group.
     *
     * @param string $taxGroupId Tax group UUID
     * @return Collection<TaxAssignment> Collection of TaxAssignment models
     */
    public function findByGroup(string $taxGroupId): Collection;

    /**
     * Find assignments by assignable entity (polymorphic).
     * Supports any entity type: region, category, product, branch, franchise, etc.
     *
     * @param string $assignableType Entity type ('region', 'category', 'product', 'branch', etc.)
     * @param string $assignableId Entity UUID
     * @return Collection<TaxAssignment> Collection of TaxAssignment models
     *
     * @example
     * // Find region assignments
     * $assignments = $repo->findByAssignable('region', $regionId);
     *
     * // Find category assignments
     * $assignments = $repo->findByAssignable('category', $categoryId);
     *
     * // Find future branch assignments
     * $assignments = $repo->findByAssignable('branch', $branchId);
     */
    public function findByAssignable(string $assignableType, string $assignableId): Collection;

    /**
     * Find assignments by multiple assignable entities.
     *
     * @param array<array{type: string, id: string}> $assignables Array of [type, id] pairs
     * @return Collection<TaxAssignment> Collection of TaxAssignment models
     *
     * @example
     * $assignables = [
     *     ['type' => 'region', 'id' => $regionId],
     *     ['type' => 'category', 'id' => $categoryId]
     * ];
     * $assignments = $repo->findByAssignables($assignables);
     */
    public function findByAssignables(array $assignables): Collection;

    /**
     * Find tax groups that are assigned to a specific entity.
     *
     * @param string $assignableType Entity type
     * @param string $assignableId Entity UUID
     * @return Collection Collection of tax group IDs and names
     */
    public function findTaxGroupsForEntity(string $assignableType, string $assignableId): Collection;

    /**
     * Find overlapping assignments (same entity assigned to multiple tax groups).
     *
     * @param string $assignableType Entity type
     * @param string $assignableId Entity UUID
     * @return Collection<TaxAssignment> Collection of overlapping assignments
     */
    public function findOverlappingAssignments(string $assignableType, string $assignableId): Collection;

    /**
     * Create a new tax assignment.
     *
     * @param array{
     *     tax_group_id: string,
     *     assignable_type: string,
     *     assignable_id: string
     * } $data Assignment data
     * @return TaxAssignment Created TaxAssignment model
     */
    public function create(array $data): TaxAssignment;

    /**
     * Create multiple assignments in bulk.
     *
     * @param string $taxGroupId Tax group UUID
     * @param array<array{type: string, id: string}> $assignables Array of assignable entities
     * @return Collection<TaxAssignment> Collection of created assignments
     *
     * @example
     * $assignables = [
     *     ['type' => 'region', 'id' => 'region-1'],
     *     ['type' => 'category', 'id' => 'category-1']
     * ];
     * $assignments = $repo->createBulk($taxGroupId, $assignables);
     */
    public function createBulk(string $taxGroupId, array $assignables): Collection;

    /**
     * Delete assignments by tax group.
     *
     * @param string $taxGroupId Tax group UUID
     * @return int Number of deleted assignments
     */
    public function deleteByGroup(string $taxGroupId): int;

    /**
     * Delete assignments by assignable entity.
     *
     * @param string $assignableType Entity type
     * @param string $assignableId Entity UUID
     * @return int Number of deleted assignments
     */
    public function deleteByAssignable(string $assignableType, string $assignableId): int;

    /**
     * Delete multiple assignments in bulk.
     *
     * @param string $taxGroupId Tax group UUID
     * @param array<array{type: string, id: string}> $assignables Array of assignable entities
     * @return int Number of deleted assignments
     */
    public function deleteBulk(string $taxGroupId, array $assignables): int;

    /**
     * Delete a specific assignment by ID.
     *
     * @param string $id Assignment UUID
     * @return bool True if deletion successful
     */
    public function delete(string $id): bool;

    // Legacy methods for backward compatibility
    /**
     * @deprecated Use findByAssignable('product', $productId) instead
     */
    public function findByProduct(string $productId): Collection;

    /**
     * @deprecated Use findByAssignable('category', $categoryId) instead
     */
    public function findByCategory(string $categoryId): Collection;

    /**
     * @deprecated Use findByAssignable('region', $regionId) instead
     */
    public function findByRegion(string $regionId): Collection;
}
