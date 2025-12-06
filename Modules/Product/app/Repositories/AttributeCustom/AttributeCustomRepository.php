<?php

namespace Modules\Product\Repositories\AttributeCustom;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Cache\KeyManager\IKeyManager;
use Modules\Product\Models\AttributeCustom;

class AttributeCustomRepository implements IAttributeCustomRepository
{
    protected AttributeCustom $model;
    protected Cache $cache;
    protected IKeyManager $cacheKeyManager;
    protected int $cacheTtl = 900; // 15 minutes

    public function __construct(AttributeCustom $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    public function findById(string $id): ?AttributeCustom
    {
        return $this->model->find($id);
    }

    public function getByMerchantId(string $merchantId): Collection
    {
        $cacheKey = $this->cacheKeyManager::merchantAttributes($merchantId);
        return $this->cache->remember(
            $cacheKey,
            $this->cacheTtl,
            fn() =>
            $this->model->where('merchant_id', $merchantId)->orderBy('name')->get()
        );
    }

    public function create(array $data): AttributeCustom
    {
        $attribute = $this->model->create($data);
        $this->invalidateMerchantCache($attribute->merchant_id);
        return $attribute;
    }

    public function update(string $id, array $data): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        $oldMerchantId = $attribute->merchant_id;
        $result = $attribute->update($data);

        if ($result) {
            $this->invalidateMerchantCache($oldMerchantId);
            if (isset($data['merchant_id']) && $data['merchant_id'] !== $oldMerchantId) {
                $this->invalidateMerchantCache($data['merchant_id']);
            }
        }

        return $result;
    }

    public function delete(string $id): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        $result = $attribute->delete();
        if ($result) $this->invalidateMerchantCache($attribute->merchant_id);

        return $result;
    }

    public function existsForMerchant(string $merchantId, string $key, ?string $excludeId = null): bool
    {
        $query = $this->model->where('merchant_id', $merchantId)->where('key', $key);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }

    protected function invalidateMerchantCache(string $merchantId): void
    {
        $this->cache->forget($this->cacheKeyManager::merchantAttributes($merchantId));
    }
}
