<?php

namespace Modules\Product\Repositories\AttributeMaster;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Cache\KeyManager\IKeyManager;
use Modules\Product\Models\AttributeMaster;

/**
 * AttributeMaster Repository Implementation
 */
class AttributeMasterRepository implements IAttributeMasterRepository
{
    protected AttributeMaster $model;
    protected Cache $cache;
    protected IKeyManager $cacheKeyManager;
    protected int $cacheTtl = 1800; // 30 minutes

    public function __construct(AttributeMaster $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    public function findById(string $id): ?AttributeMaster
    {
        $cacheKey = $this->cacheKeyManager::attributeMasterById($id);
        return $this->cache->remember($cacheKey, $this->cacheTtl, fn() => $this->model->find($id));
    }

    public function findByKey(string $key): ?AttributeMaster
    {
        $cacheKey = $this->cacheKeyManager::attributeMasterByKey($key);
        return $this->cache->remember($cacheKey, $this->cacheTtl, fn() => $this->model->where('key', $key)->first());
    }

    public function getAll(): Collection
    {
        $cacheKey = $this->cacheKeyManager::allAttributeMasters();
        return $this->cache->remember($cacheKey, $this->cacheTtl, fn() => $this->model->orderBy('name')->get());
    }

    public function create(array $data): AttributeMaster
    {
        $attribute = $this->model->create($data);
        $this->invalidateCaches();
        return $attribute;
    }

    public function update(string $id, array $data): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        $oldKey = $attribute->key;
        $result = $attribute->update($data);

        if ($result) {
            if (isset($data['key']) && $data['key'] !== $oldKey) {
                $this->cache->forget($this->cacheKeyManager::attributeMasterByKey($oldKey));
            }
            $this->invalidateCaches();
        }

        return $result;
    }

    public function delete(string $id): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        $result = $attribute->delete();
        if ($result) $this->invalidateCaches();

        return $result;
    }

    public function existsByKey(string $key, ?string $excludeId = null): bool
    {
        $query = $this->model->where('key', $key);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }

    protected function invalidateCaches(): void
    {
        $this->cache->forget($this->cacheKeyManager::allAttributeMasters());
    }
}
