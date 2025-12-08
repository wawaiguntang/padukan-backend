<?php

namespace Modules\Product\Repositories\UnitConversion;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Product\Cache\UnitConversion\UnitConversionKeyManager;
use Modules\Product\Cache\UnitConversion\UnitConversionCacheManager;
use Modules\Product\Cache\UnitConversion\UnitConversionTtlManager;
use Modules\Product\Models\UnitConversion;

class UnitConversionRepository implements IUnitConversionRepository
{
    protected UnitConversion $model;

    public function __construct(UnitConversion $model)
    {
        $this->model = $model;
    }

    public function findById(string $id): ?UnitConversion
    {
        $cacheKey = UnitConversionKeyManager::unitConversionById($id);
        $ttl = UnitConversionTtlManager::unitConversionEntity();

        return Cache::remember($cacheKey, $ttl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function getConversions(): Collection
    {
        $cacheKey = UnitConversionKeyManager::allUnitConversions();
        $ttl = UnitConversionTtlManager::unitConversionList();

        return Cache::remember($cacheKey, $ttl, function () {
            return $this->model->orderBy('from_unit')->get();
        });
    }

    public function getByUnit(string $unit): Collection
    {
        $cacheKey = UnitConversionKeyManager::unitConversionsByUnit($unit);
        $ttl = UnitConversionTtlManager::unitConversionLookup();

        return Cache::remember($cacheKey, $ttl, function () use ($unit) {
            return $this->model->where('from_unit', $unit)
                ->orWhere('to_unit', $unit)
                ->orderBy('from_unit')
                ->get();
        });
    }

    public function create(array $data): UnitConversion
    {
        $conversion = $this->model->create($data);

        UnitConversionCacheManager::invalidateForOperation('create', [
            'from_unit' => $conversion->from_unit,
            'to_unit' => $conversion->to_unit,
        ]);

        return $conversion;
    }

    public function update(string $id, array $data): bool
    {
        $conversion = $this->model->find($id);
        if (!$conversion) return false;

        $oldData = $conversion->toArray();
        $result = $conversion->update($data);

        if ($result) {
            UnitConversionCacheManager::invalidateForOperation('update', [
                'id' => $id,
                'data' => $data,
                'old_data' => $oldData,
            ]);
        }

        return $result;
    }

    public function delete(string $id): bool
    {
        $conversion = $this->model->find($id);
        if (!$conversion) return false;

        $result = $conversion->delete();
        if ($result) {
            UnitConversionCacheManager::invalidateForOperation('delete', [
                'id' => $id,
                'unit_conversion' => $conversion->toArray(),
            ]);
        }

        return $result;
    }
}
