<?php

namespace Modules\Product\Repositories\AttributeCustom;

use Illuminate\Database\Eloquent\Collection;
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
        return $this->model->where('merchant_id', $merchantId)->orderBy('name')->get();
    }

    public function create(array $data): AttributeCustom
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        return $attribute->update($data);
    }

    public function delete(string $id): bool
    {
        $attribute = $this->model->find($id);
        if (!$attribute) return false;

        return $attribute->delete();
    }

    public function existsForMerchant(string $merchantId, string $key, ?string $excludeId = null): bool
    {
        $query = $this->model->where('merchant_id', $merchantId)->where('key', $key);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }
}
