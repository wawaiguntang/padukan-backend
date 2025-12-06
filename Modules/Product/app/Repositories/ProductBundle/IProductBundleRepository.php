<?php

namespace Modules\Product\Repositories\ProductBundle;

use Modules\Product\Models\ProductBundle;

interface IProductBundleRepository
{
    public function findById(string $id): ?ProductBundle;
    public function create(array $data): ProductBundle;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function existsByName(string $name, ?string $excludeId = null): bool;
}
