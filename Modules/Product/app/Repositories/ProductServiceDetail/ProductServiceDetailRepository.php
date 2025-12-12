<?php

namespace Modules\Product\Repositories\ProductServiceDetail;

use Modules\Product\Models\ProductServiceDetail;

class ProductServiceDetailRepository implements IProductServiceDetailRepository
{
    protected ProductServiceDetail $model;

    public function __construct(ProductServiceDetail $model)
    {
        $this->model = $model;
    }

    public function findById(string $serviceId): ?ProductServiceDetail
    {
        return $this->model->find($serviceId);
    }

    public function findByProductId(string $productId): ?ProductServiceDetail
    {
        return $this->model->where('product_id', $productId)->first();
    }

    public function create(array $data): ProductServiceDetail
    {
        return $this->model->create($data);
    }

    public function update(string $serviceId, array $data): bool
    {
        $serviceDetail = $this->model->find($serviceId);
        if (!$serviceDetail) return false;

        return $serviceDetail->update($data);
    }

    public function delete(string $serviceId): bool
    {
        $serviceDetail = $this->model->find($serviceId);
        if (!$serviceDetail) return false;

        return $serviceDetail->delete();
    }
}
