<?php

namespace Modules\Product\Repositories\AttributeMaster;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\AttributeMaster;

/**
 * Interface for AttributeMaster Repository
 */
interface IAttributeMasterRepository
{
    public function findById(string $id): ?AttributeMaster;
    public function findByKey(string $key): ?AttributeMaster;
    public function getAll(): Collection;
    public function create(array $data): AttributeMaster;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    public function existsByKey(string $key, ?string $excludeId = null): bool;
}
