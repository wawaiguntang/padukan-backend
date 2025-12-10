<?php

namespace Modules\Product\Repositories\ProductImage;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductImage;

interface IProductImageRepository
{
    public function findById(string $id): ?ProductImage;
    public function getByProductId(string $productId): Collection;
    public function create(array $data): ProductImage;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function deleteByProductId(string $productId): bool;
    public function setPrimary(string $productId, string $imageId): bool;
}
