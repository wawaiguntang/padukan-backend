<?php

namespace Modules\Tax\Repositories\TaxGroup;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Tax\Cache\TaxCacheManager;
use Modules\Tax\Cache\TaxKeyManager;
use Modules\Tax\Cache\TaxTtlManager;
use Modules\Tax\Models\TaxGroup;

class TaxGroupRepository implements ITaxGroupRepository
{
    private TaxGroup $model;

    public function __construct(TaxGroup $model)
    {
        $this->model = $model;
    }

    public function findAllActive(): Collection
    {
        $key = TaxKeyManager::allTaxGroups();
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_group'))->remember($key, $ttl, function () {
            return $this->model->where('is_active', true)->get();
        });
    }

    public function findById(string $id): ?TaxGroup
    {
        $key = TaxKeyManager::taxGroupById($id);
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_group'))->remember($key, $ttl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function findByIdWithDetails(string $id): ?TaxGroup
    {
        $key = TaxKeyManager::taxGroupByIdWithDetails($id);
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_group'))->remember($key, $ttl, function () use ($id) {
            return $this->model->with(['rates.tax', 'assignments'])->find($id);
        });
    }

    public function findByOwner(string $ownerId, string $ownerType): Collection
    {
        $key = TaxKeyManager::taxGroupsByOwner($ownerId, $ownerType);
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_owner'))->remember($key, $ttl, function () use ($ownerId, $ownerType) {
            return $this->model
                ->where('owner_id', $ownerId)
                ->where('owner_type', $ownerType)
                ->get();
        });
    }

    public function findByOwnerWithDetails(string $ownerId, string $ownerType): Collection
    {
        $key = TaxKeyManager::taxGroupsByOwnerWithDetails($ownerId, $ownerType);
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_owner'))->remember($key, $ttl, function () use ($ownerId, $ownerType) {
            return $this->model
                ->with(['rates.tax', 'assignments'])
                ->where('owner_id', $ownerId)
                ->where('owner_type', $ownerType)
                ->get();
        });
    }

    /**
     * Find tax groups by owner type (dynamic method).
     * Supports any owner type without code changes.
     */
    public function findByOwnerType(string $ownerType, ?string $ownerId = null): Collection
    {
        // Use existing system method for system type
        if ($ownerType === 'system') {
            return $this->findSystemTaxGroups();
        }

        // For other types, use generic query
        $key = TaxKeyManager::taxGroupsByOwner($ownerId ?: '', $ownerType);
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_owner'))->remember($key, $ttl, function () use ($ownerId, $ownerType) {
            $query = $this->model->where('owner_type', $ownerType);

            if ($ownerId) {
                $query->where('owner_id', $ownerId);
            }

            return $query->where('is_active', true)->get();
        });
    }

    /**
     * Find tax group with assignments loaded.
     */
    public function findWithAssignments(string $id): ?TaxGroup
    {
        $key = "tax:group:{$id}:with_assignments";
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_group'))->remember($key, $ttl, function () use ($id) {
            return $this->model->with('assignments')->find($id);
        });
    }

    /**
     * Find tax groups assigned to a specific entity.
     */
    public function findByAssignedEntity(string $entityType, string $entityId): Collection
    {
        $key = "tax:groups:assigned:{$entityType}:{$entityId}";
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_assignment'))->remember($key, $ttl, function () use ($entityType, $entityId) {
            return $this->model
                ->whereHas('assignments', function ($query) use ($entityType, $entityId) {
                    $query->where('assignable_type', $entityType)
                        ->where('assignable_id', $entityId);
                })
                ->get();
        });
    }

    /**
     * @deprecated Use findByOwnerType('system') instead
     */
    public function findSystemTaxGroups(): Collection
    {
        $key = TaxKeyManager::systemTaxes(); // Reuse system taxes key
        $ttl = TaxTtlManager::getTaxGroupTtl();

        return Cache::tags(TaxCacheManager::getTag('tax_owner'))->remember($key, $ttl, function () {
            return $this->model
                ->where('owner_type', 'system')
                ->whereNull('owner_id')
                ->get();
        });
    }

    public function findByIds(array $ids): Collection
    {
        // For multiple IDs, we don't cache as it could be too many combinations
        return $this->model->whereIn('id', $ids)->get();
    }

    /**
     * Create multiple tax groups in bulk.
     */
    public function createBulk(array $groupsData): Collection
    {
        $createdGroups = collect();

        foreach ($groupsData as $groupData) {
            $createdGroups->push($this->create($groupData));
        }

        // Invalidate all tax groups cache after bulk operation
        \Modules\Tax\Cache\TaxCacheManager::invalidateAllTaxGroups();

        return $createdGroups;
    }

    public function create(array $data): TaxGroup
    {
        $group = $this->model->create($data);
        // Cache invalidation should be handled in the service layer
        return $group;
    }

    public function update(string $id, array $data): bool
    {
        $group = $this->findById($id);
        if (!$group) {
            return false;
        }

        $result = $group->update($data);
        // Cache invalidation should be handled in the service layer
        return $result;
    }

    public function delete(string $id): bool
    {
        $group = $this->findById($id);
        if (!$group) {
            return false;
        }

        $result = $group->delete();
        // Cache invalidation should be handled in the service layer
        return $result;
    }
}
