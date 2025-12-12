<?php

namespace Modules\Tax\Repositories\Tax;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Tax\Cache\TaxCacheManager;
use Modules\Tax\Cache\TaxKeyManager;
use Modules\Tax\Cache\TaxTtlManager;
use Modules\Tax\Models\Tax;

class TaxRepository implements ITaxRepository
{
    private Tax $model;

    public function __construct(Tax $model)
    {
        $this->model = $model;
    }

    public function findAllActive(): Collection
    {
        $key = TaxKeyManager::allTaxes();
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () {
            return $this->model->where('is_active', true)->get();
        });
    }

    public function findById(string $id): ?Tax
    {
        $key = TaxKeyManager::taxById($id);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function findBySlug(string $slug): ?Tax
    {
        $key = TaxKeyManager::taxBySlug($slug);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($slug) {
            return $this->model->where('slug', $slug)->first();
        });
    }

    public function findByGroup(string $groupId): Collection
    {
        $key = TaxKeyManager::taxesByGroup($groupId);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($groupId) {
            return $this->model
                ->whereHas('groups', function ($query) use ($groupId) {
                    $query->where('tax_group_id', $groupId);
                })
                ->get();
        });
    }

    public function findByOwner(string $ownerId, string $ownerType): Collection
    {
        $key = TaxKeyManager::taxesByOwner($ownerId, $ownerType);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($ownerId, $ownerType) {
            return $this->model
                ->whereHas('groups', function ($query) use ($ownerId, $ownerType) {
                    $query->where('owner_id', $ownerId)->where('owner_type', $ownerType);
                })
                ->get();
        });
    }

    public function findByGroupIds(array $groupIds): Collection
    {
        // Note: Caching for this method might be complex due to the array of IDs.
        // For now, it's not cached.
        return $this->model
            ->whereHas('groups', function ($query) use ($groupIds) {
                $query->whereIn('tax_group_id', $groupIds);
            })
            ->get();
    }

    public function findSystemTaxes(): Collection
    {
        $key = TaxKeyManager::systemTaxes();
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () {
            return $this->model
                ->where('owner_type', 'system')
                ->orWhereNull('owner_type')
                ->where('is_active', true)
                ->get();
        });
    }

    /**
     * Find taxes by owner type (dynamic implementation).
     * Supports any owner type without code changes.
     *
     * @param string $ownerType Owner entity type
     * @param string|null $ownerId Specific owner UUID, null for system/global
     * @return Collection<Tax>
     */
    public function findByOwnerType(string $ownerType, ?string $ownerId = null): Collection
    {
        // Use existing system taxes method for system type
        if ($ownerType === 'system') {
            return $this->findSystemTaxes();
        }

        // For other types, use generic query
        $key = TaxKeyManager::taxesByOwner($ownerId ?: '', $ownerType);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($ownerId, $ownerType) {
            $query = $this->model->where('owner_type', $ownerType);

            if ($ownerId) {
                $query->where('owner_id', $ownerId);
            }

            return $query->where('is_active', true)->get();
        });
    }

    /**
     * Find taxes for a specific merchant (legacy method).
     * @deprecated Use findByOwnerType('merchant', $merchantId) instead
     */
    public function findMerchantTaxes(string $merchantId): Collection
    {
        return $this->findByOwnerType('merchant', $merchantId);
    }

    /**
     * Create a new tax.
     */
    public function create(array $data): Tax
    {
        return $this->model->create($data);
    }

    /**
     * Create multiple taxes in bulk operation.
     *
     * @param array<array{
     *     owner_type: string,
     *     owner_id: string|null,
     *     name: string,
     *     slug: string,
     *     description: string|null,
     *     is_active: bool
     * }> $taxesData
     * @return Collection<Tax>
     */
    public function createBulk(array $taxesData): Collection
    {
        $createdTaxes = collect();

        foreach ($taxesData as $taxData) {
            $createdTaxes->push($this->create($taxData));
        }

        // Invalidate all tax caches after bulk operation
        TaxCacheManager::invalidateAllTaxes();

        return $createdTaxes;
    }

    public function update(string $id, array $data): bool
    {
        $tax = $this->findById($id);
        if (!$tax) {
            return false;
        }

        return $tax->update($data);
    }

    public function delete(string $id): bool
    {
        $tax = $this->findById($id);
        if (!$tax) {
            return false;
        }

        return $tax->delete();
    }
}
