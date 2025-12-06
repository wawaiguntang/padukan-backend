<?php

namespace Modules\Product\Repositories\ProductExtra;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductExtra;

class ProductExtraRepository implements IProductExtraRepository
{
    protected ProductExtra $model;

    public function __construct(ProductExtra $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?ProductExtra
    {
        return $this->model->find($id);
    }

    public function getByProductId(string $productId): Collection
    {
        return $this->model->where('product_id', $productId)->orderBy('name')->get();
    }

    public function create(array $data): ProductExtra
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $extra = $this->model->find($id);
        return $extra ? $extra->update($data) : false;
    }

    public function delete(string $id): bool
    {
        $extra = $this->model->find($id);
        return $extra ? $extra->delete() : false;
    }
}
