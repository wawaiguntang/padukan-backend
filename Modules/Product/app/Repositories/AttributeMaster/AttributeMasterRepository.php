<?php

namespace Modules\Product\Repositories\AttributeMaster;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\AttributeMaster;

/**
 * AttributeMaster Repository Implementation
 */
class AttributeMasterRepository implements IAttributeMasterRepository
{
    protected AttributeMaster $model;

    public function __construct(AttributeMaster $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?AttributeMaster
    {
        return $this->model->find($id);
    }

    public function findByKey(string $key): ?AttributeMaster
    {
        return $this->model->where('key', $key)->first();
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function create(array $data): AttributeMaster
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

    public function existsByKey(string $key, ?string $excludeId = null): bool
    {
        $query = $this->model->where('key', $key);
        if ($excludeId) $query->where('id', '!=', $excludeId);
        return $query->exists();
    }
}
