<?php

namespace Modules\Tax\Repositories\TaxAssignment;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Tax\Cache\TaxCacheManager;
use Modules\Tax\Cache\TaxKeyManager;
use Modules\Tax\Cache\TaxTtlManager;
use Modules\Tax\Models\TaxAssignment;

class TaxAssignmentRepository implements ITaxAssignmentRepository
{
    private TaxAssignment $model;

    public function __construct(TaxAssignment $model)
    {
        $this->model = $model;
    }

    /**
     * Find all assignments for a specific tax group.
     */
    public function findByGroup(string $taxGroupId): Collection
    {
        $key = TaxKeyManager::assignmentsByGroup($taxGroupId);
        $ttl = TaxTtlManager::getTaxListTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_assignment'))->remember($key, $ttl, function () use ($taxGroupId) {
            return $this->model->where('tax_group_id', $taxGroupId)->get();
        });
    }

    /**
     * Find assignments by assignable entity (polymorphic).
     * Supports any entity type without code changes.
     */
    public function findByAssignable(string $assignableType, string $assignableId): Collection
    {
        $key = "tax:assignment:{$assignableType}:{$assignableId}";
        $ttl = TaxTtlManager::getTaxListTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_assignment'))->remember($key, $ttl, function () use ($assignableType, $assignableId) {
            return $this->model
                ->where('assignable_type', $assignableType)
                ->where('assignable_id', $assignableId)
                ->get();
        });
    }

    /**
     * Find assignments by multiple assignable entities.
     */
    public function findByAssignables(array $assignables): Collection
    {
        $assignments = collect();

        foreach ($assignables as $assignable) {
            $assignments = $assignments->merge(
                $this->findByAssignable($assignable['type'], $assignable['id'])
            );
        }

        return $assignments;
    }

    /**
     * Find tax groups assigned to a specific entity.
     */
    public function findTaxGroupsForEntity(string $assignableType, string $assignableId): Collection
    {
        return $this->model
            ->where('assignable_type', $assignableType)
            ->where('assignable_id', $assignableId)
            ->with('group')
            ->get()
            ->pluck('group')
            ->unique('id');
    }

    /**
     * Find overlapping assignments for the same entity.
     */
    public function findOverlappingAssignments(string $assignableType, string $assignableId): Collection
    {
        $assignments = $this->findByAssignable($assignableType, $assignableId);

        // Group by tax group to find overlaps
        $grouped = $assignments->groupBy('tax_group_id');

        // Return assignments where entity is assigned to multiple groups
        return $assignments->filter(function ($assignment) use ($grouped) {
            return $grouped[$assignment->tax_group_id]->count() > 1;
        });
    }

    /**
     * Create a new tax assignment.
     */
    public function create(array $data): TaxAssignment
    {
        $assignment = $this->model->create($data);

        // Invalidate relevant caches
        $this->invalidateAssignmentCaches($assignment);

        return $assignment;
    }

    /**
     * Create multiple assignments in bulk.
     */
    public function createBulk(string $taxGroupId, array $assignables): Collection
    {
        $assignments = collect();

        foreach ($assignables as $assignable) {
            $data = [
                'tax_group_id' => $taxGroupId,
                'assignable_type' => $assignable['type'],
                'assignable_id' => $assignable['id'],
            ];

            $assignments->push($this->create($data));
        }

        return $assignments;
    }

    /**
     * Delete assignments by tax group.
     */
    public function deleteByGroup(string $taxGroupId): int
    {
        $assignments = $this->findByGroup($taxGroupId);

        foreach ($assignments as $assignment) {
            $this->invalidateAssignmentCaches($assignment);
        }

        return $this->model->where('tax_group_id', $taxGroupId)->delete();
    }

    /**
     * Delete assignments by assignable entity.
     */
    public function deleteByAssignable(string $assignableType, string $assignableId): int
    {
        $assignments = $this->findByAssignable($assignableType, $assignableId);

        foreach ($assignments as $assignment) {
            $this->invalidateAssignmentCaches($assignment);
        }

        return $this->model
            ->where('assignable_type', $assignableType)
            ->where('assignable_id', $assignableId)
            ->delete();
    }

    /**
     * Delete multiple assignments in bulk.
     */
    public function deleteBulk(string $taxGroupId, array $assignables): int
    {
        $deletedCount = 0;

        foreach ($assignables as $assignable) {
            $deletedCount += $this->model
                ->where('tax_group_id', $taxGroupId)
                ->where('assignable_type', $assignable['type'])
                ->where('assignable_id', $assignable['id'])
                ->delete();
        }

        // Invalidate caches
        TaxCacheManager::invalidateAssignmentsByGroup($taxGroupId);

        return $deletedCount;
    }

    /**
     * Delete a specific assignment by ID.
     */
    public function delete(string $id): bool
    {
        $assignment = $this->model->find($id);

        if ($assignment) {
            $this->invalidateAssignmentCaches($assignment);
            return $assignment->delete();
        }

        return false;
    }

    // Legacy methods for backward compatibility
    /**
     * @deprecated Use findByAssignable('product', $productId) instead
     */
    public function findByProduct(string $productId): Collection
    {
        return $this->findByAssignable('product', $productId);
    }

    /**
     * @deprecated Use findByAssignable('category', $categoryId) instead
     */
    public function findByCategory(string $categoryId): Collection
    {
        return $this->findByAssignable('category', $categoryId);
    }

    /**
     * @deprecated Use findByAssignable('region', $regionId) instead
     */
    public function findByRegion(string $regionId): Collection
    {
        return $this->findByAssignable('region', $regionId);
    }

    /**
     * Invalidate caches for an assignment.
     */
    private function invalidateAssignmentCaches(TaxAssignment $assignment): void
    {
        TaxCacheManager::invalidateAssignmentsByGroup($assignment->tax_group_id);

        // Invalidate entity-specific cache using tags
        $cacheKey = "tax:assignment:{$assignment->assignable_type}:{$assignment->assignable_id}";
        Cache::tags(TaxCacheManager::getTag('tax_assignment'))->forget($cacheKey);
    }
}
