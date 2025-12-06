<?php

namespace Modules\Product\Repositories\ProductExtra;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\ProductExtra;

interface IProductExtraRepository
{
    public function findById(string $id): ?ProductExtra;
    public function getByProductId(string $productId): Collection;
    public function create(array $data): ProductExtra;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
}
