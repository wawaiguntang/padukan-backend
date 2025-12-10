<?php

namespace Modules\Region\Services\Region;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Region\Models\Region;
use Modules\Region\Repositories\Region\IRegionRepository;

class RegionService implements IRegionService
{
    protected IRegionRepository $regionRepository;

    public function __construct(IRegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    /**
     * @param array{name?: string, is_active?: bool} $filters
     */
    public function getAllRegions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->regionRepository->getAll($filters, $perPage);
    }

    /**
     * @param array{name: string, slug: string, polygon: array, timezone?: string, currency_code?: string, is_active?: bool} $data
     */
    public function createRegion(array $data): Region
    {
        return $this->regionRepository->create($data);
    }

    public function findRegionById(string $regionId): Region
    {
        $region = $this->regionRepository->findById($regionId);
        if (!$region) {
            throw new ModelNotFoundException("Region with ID {$regionId} not found.");
        }
        return $region;
    }

    /**
     * @param array{name?: string, slug?: string, polygon?: array, timezone?: string, currency_code?: string, is_active?: bool} $data
     */
    public function updateRegion(string $regionId, array $data): Region
    {
        $region = $this->findRegionById($regionId);
        $this->regionRepository->update($regionId, $data);
        return $region->fresh();
    }

    public function deleteRegion(string $regionId): bool
    {
        return $this->regionRepository->delete($regionId);
    }

    public function toggleActivationStatus(string $regionId): Region
    {
        $region = $this->findRegionById($regionId);
        $region->is_active = !$region->is_active;
        $region->save();
        return $region;
    }

    /**
     * @param list<string> $serviceIds
     */
    public function syncServicesForRegion(string $regionId, array $serviceIds): void
    {
        $region = $this->findRegionById($regionId);
        $region->services()->sync($serviceIds);
    }
}
