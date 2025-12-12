<?php

namespace Modules\Product\Repositories\ProductBundle;

use Modules\Product\Models\ProductBundle;

class ProductBundleRepository implements IProductBundleRepository
{
    protected ProductBundle $model;

    public function __construct(ProductBundle $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?ProductBundle
    {
        return $this->model->find($id);
    }

    public function create(array $data): ProductBundle
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $bundle = $this->model->find($id);
        return $bundle ? $bundle->update($data) : false;
    }

    public function delete(string $id): bool
    {
        $bundle = $this->model->find($id);
        return $bundle ? $bundle->delete() : false;
    }

    public function existsByName(string $name, ?string $excludeId = null): bool
    {
        $query = $this->model->where('name', $name);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }
}
