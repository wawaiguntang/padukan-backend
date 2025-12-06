<?php

namespace Modules\Product\Repositories\ProductServiceDetail;

use Modules\Product\Models\ProductServiceDetail;

interface IProductServiceDetailRepository
{
    public function findById(string $serviceId): ?ProductServiceDetail;
    public function findByProductId(string $productId): ?ProductServiceDetail;
    public function create(array $data): ProductServiceDetail;
    public function update(string $serviceId, array $data): bool;
    public function delete(string $serviceId): bool;
}
