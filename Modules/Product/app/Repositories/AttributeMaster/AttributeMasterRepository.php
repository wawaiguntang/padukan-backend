<?php

namespace Modules\Product\Repositories\AttributeMaster;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Cache\AttributeMaster\AttributeMasterKeyManager;
use Modules\Product\Cache\AttributeMaster\AttributeMasterCacheManager;
use Modules\Product\Cache\AttributeMaster\AttributeMasterTtlManager;
use Modules\Product\Models\AttributeMaster;

/**
 * AttributeMaster Repository Implementation
 */
class AttributeMasterRepository implements IAttributeMasterRepository
{
    protected AttributeMaster $model;

    public function __construct(AttributeMaster $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?AttributeMaster
    {
        $cacheKey = AttributeMasterKeyManager::attributeMasterById($id);
        $ttl = AttributeMasterTtlManager::attributeMasterEntity();

        return Cache::remember($cacheKey, $ttl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function findByKey(string $key): ?AttributeMaster
    {
        $cacheKey = AttributeMasterKeyManager::attributeMasterByKey($key);
        $ttl = AttributeMasterTtlManager::attributeMasterLookup();

        return Cache::remember($cacheKey, $ttl, function () use ($key) {
            return $this->model->where('key', $key)->first();
        });
    }

    public function getAll(): Collection
    {
        $cacheKey = AttributeMasterKeyManager::allAttributeMasters();
        $ttl = AttributeMasterTtlManager::attributeMasterList();

        return Cache::remember($cacheKey, $ttl, function () {
            return $this->model->orderBy('name')->get();
        });
    }

    public function create(array $data): AttributeMaster
    {
        $attribute = $this->model->create($data);

        AttributeMasterCacheManager::invalidateForOperation('create', [
            'key' => $attribute->key,
        ]);

        return $attribute;
    }

    public function update(string $id, array $data): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        $oldData = $attribute->toArray();
        $result = $attribute->update($data);

        if ($result) {
            AttributeMasterCacheManager::invalidateForOperation('update', [
                'id' => $id,
                'data' => $data,
                'old_data' => $oldData,
            ]);
        }

        return $result;
    }

    public function delete(string $id): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        $result = $attribute->delete();
        if ($result) {
            AttributeMasterCacheManager::invalidateForOperation('delete', [
                'id' => $id,
                'attribute_master' => $attribute->toArray(),
            ]);
        }

        return $result;
    }

    public function existsByKey(string $key, ?string $excludeId = null): bool
    {
        $query = $this->model->where('key', $key);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }
}
