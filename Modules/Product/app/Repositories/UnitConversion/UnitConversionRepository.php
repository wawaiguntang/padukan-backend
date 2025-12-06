<?php

namespace Modules\Product\Repositories\UnitConversion;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Cache\KeyManager\IKeyManager;
use Modules\Product\Models\UnitConversion;

class UnitConversionRepository implements IUnitConversionRepository
{
    protected UnitConversion $model;
    protected Cache $cache;
    protected IKeyManager $cacheKeyManager;
    protected int $cacheTtl = 3600; // 1 hour

    public function __construct(UnitConversion $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    public function findById(string $id): ?UnitConversion
    {
        return $this->model->find($id);
    }

    public function getConversions(): Collection
    {
        $cacheKey = $this->cacheKeyManager::unitConversions();
        return $this->cache->remember(
            $cacheKey,
            $this->cacheTtl,
            fn() =>
            $this->model->orderBy('from_unit')->get()
        );
    }

    public function getByUnit(string $unit): Collection
    {
        return $this->model->where('from_unit', $unit)
            ->orWhere('to_unit', $unit)
            ->orderBy('from_unit')
            ->get();
    }

    public function create(array $data): UnitConversion
    {
        $conversion = $this->model->create($data);
        $this->invalidateCaches();
        return $conversion;
    }

    public function update(string $id, array $data): bool
    {
        $conversion = $this->model->find($id);
        if (!$conversion) return false;

        $result = $conversion->update($data);
        if ($result) $this->invalidateCaches();

        return $result;
    }

    public function delete(string $id): bool
    {
        $conversion = $this->model->find($id);
        if (!$conversion) return false;

        $result = $conversion->delete();
        if ($result) $this->invalidateCaches();

        return $result;
    }

    protected function invalidateCaches(): void
    {
        $this->cache->forget($this->cacheKeyManager::unitConversions());
    }
}
