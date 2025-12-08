<?php

namespace Modules\Product\Repositories\AttributeCustom;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Cache\AttributeCustom\AttributeCustomKeyManager;
use Modules\Product\Cache\AttributeCustom\AttributeCustomCacheManager;
use Modules\Product\Cache\AttributeCustom\AttributeCustomTtlManager;
use Modules\Product\Models\AttributeCustom;

class AttributeCustomRepository implements IAttributeCustomRepository
{
    protected AttributeCustom $model;

    public function __construct(AttributeCustom $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?AttributeCustom
    {
        return $this->model->find($id);
    }

    public function getByMerchantId(string $merchantId): Collection
    {
        $cacheKey = AttributeCustomKeyManager::merchantAttributes($merchantId);
        $ttl = AttributeCustomTtlManager::attributeList();

        return Cache::remember($cacheKey, $ttl, function () use ($merchantId) {
            return $this->model->where('merchant_id', $merchantId)->orderBy('name')->get();
        });
    }

    public function create(array $data): AttributeCustom
    {
        $attribute = $this->model->create($data);

        AttributeCustomCacheManager::invalidateForOperation('create', [
            'merchant_id' => $attribute->merchant_id
        ]);

        return $attribute;
    }

    public function update(string $id, array $data): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        $oldMerchantId = $attribute->merchant_id;
        $oldKey = $attribute->key;
        $result = $attribute->update($data);

        if ($result) {
            AttributeCustomCacheManager::invalidateForOperation('update', [
                'id' => $id,
                'data' => $data,
                'old_merchant_id' => $oldMerchantId,
                'old_key' => $oldKey
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
            AttributeCustomCacheManager::invalidateForOperation('delete', [
                'id' => $id,
                'attribute' => $attribute->toArray()
            ]);
        }

        return $result;
    }

    public function existsForMerchant(string $merchantId, string $key, ?string $excludeId = null): bool
    {
        $query = $this->model->where('merchant_id', $merchantId)->where('key', $key);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }
}
