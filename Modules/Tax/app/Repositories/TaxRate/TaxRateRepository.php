<?php

namespace Modules\Tax\Repositories\TaxRate;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Tax\Cache\TaxKeyManager;
use Modules\Tax\Cache\TaxTtlManager;
use Modules\Tax\Models\TaxRate;

class TaxRateRepository implements ITaxRateRepository
{
    private TaxRate $model;

    public function __construct(TaxRate $model)
    {
        $this->model = $model;
    }

    public function findAll(): Collection
    {
        $key = TaxKeyManager::allTaxRates();
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () {
            return $this->model->with(['tax', 'group'])->get();
        });
    }

    public function findById(string $id): ?TaxRate
    {
        $key = TaxKeyManager::taxRateById($id);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($id) {
            return $this->model->with(['tax', 'group'])->find($id);
        });
    }

    public function findByGroup(string $taxGroupId): Collection
    {
        $key = TaxKeyManager::taxRatesByGroup($taxGroupId);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($taxGroupId) {
            return $this->model->with(['tax', 'group'])
                ->where('tax_group_id', $taxGroupId)
                ->orderBy('priority')
                ->get();
        });
    }

    public function findByTax(string $taxId): Collection
    {
        $key = TaxKeyManager::taxRatesByTax($taxId);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($taxId) {
            return $this->model->with(['tax', 'group'])
                ->where('tax_id', $taxId)
                ->orderBy('priority')
                ->get();
        });
    }

    public function findActiveByGroup(string $taxGroupId): Collection
    {
        $key = TaxKeyManager::activeTaxRatesByGroup($taxGroupId);
        $ttl = TaxTtlManager::taxData();

        return Cache::remember($key, $ttl, function () use ($taxGroupId) {
            return $this->model->with(['tax', 'group'])
                ->where('tax_group_id', $taxGroupId)
                ->where(function ($query) {
                    $query->whereNull('valid_from')
                        ->orWhere('valid_from', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('valid_until')
                        ->orWhere('valid_until', '>=', now());
                })
                ->orderBy('priority')
                ->get();
        });
    }

    public function create(array $data): TaxRate
    {
        return $this->model->create($data);
    }

    /**
     * Create multiple tax rates in bulk for a specific group.
     */
    public function createBulkForGroup(string $taxGroupId, array $ratesData): Collection
    {
        $createdRates = collect();

        foreach ($ratesData as $rateData) {
            $rateData['tax_group_id'] = $taxGroupId;
            $createdRates->push($this->create($rateData));
        }

        // Invalidate relevant caches after bulk operation
        \Modules\Tax\Cache\TaxCacheManager::invalidateTaxRatesByGroup($taxGroupId);
        \Modules\Tax\Cache\TaxCacheManager::invalidateActiveTaxRatesByGroup($taxGroupId);
        \Modules\Tax\Cache\TaxCacheManager::invalidateAllTaxRates();

        return $createdRates;
    }

    public function update(string $id, array $data): bool
    {
        $rate = $this->findById($id);
        if (!$rate) {
            return false;
        }

        return $rate->update($data);
    }

    public function delete(string $id): bool
    {
        $rate = $this->findById($id);
        if (!$rate) {
            return false;
        }

        return $rate->delete();
    }
}
