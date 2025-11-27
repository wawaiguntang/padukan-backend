<?php

namespace Modules\Profile\Repositories\Bank;

use Modules\Profile\Models\Bank;
use Modules\Profile\Cache\KeyManager\IKeyManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * Bank Repository Implementation
 */
class BankRepository implements IBankRepository
{
    protected Bank $model;
    protected Cache $cache;
    protected IKeyManager $cacheKeyManager;
    protected int $cacheTtl = 3600; // 1 hour for master data

    public function __construct(Bank $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    public function findById(string $id): ?Bank
    {
        $cacheKey = $this->cacheKeyManager::bankById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
        });
    }

    public function findByCode(string $code): ?Bank
    {
        // Cache by code - using the same key pattern as by ID for simplicity
        $cacheKey = "profile:bank:code:{$code}";

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($code) {
            return $this->model->where('code', $code)->first();
        });
    }

    public function getActiveBanks(): Collection
    {
        $cacheKey = "profile:banks:active";

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->where('is_active', true)->get();
        });
    }

    public function create(array $data): Bank
    {
        $bank = $this->model->create($data);

        // Clear active banks cache
        $this->cache->forget("profile:banks:active");

        return $bank;
    }

    public function update(string $id, array $data): bool
    {
        $bank = $this->model->find($id);

        if (!$bank) {
            return false;
        }

        $oldCode = $bank->code;
        $result = $bank->update($data);

        if ($result) {
            // Invalidate caches
            $this->cache->forget($this->cacheKeyManager::bankById($id));
            $this->cache->forget("profile:bank:code:{$oldCode}");

            if (isset($data['code']) && $data['code'] !== $oldCode) {
                $this->cache->forget("profile:bank:code:{$data['code']}");
            }

            // Clear active banks cache if active status changed
            if (isset($data['is_active'])) {
                $this->cache->forget("profile:banks:active");
            }
        }

        return $result;
    }

    public function delete(string $id): bool
    {
        $bank = $this->model->find($id);

        if (!$bank) {
            return false;
        }

        $result = $bank->delete();

        if ($result) {
            // Invalidate all bank caches
            $this->cache->forget($this->cacheKeyManager::bankById($id));
            $this->cache->forget("profile:bank:code:{$bank->code}");
            $this->cache->forget("profile:banks:active");
        }

        return $result;
    }
}