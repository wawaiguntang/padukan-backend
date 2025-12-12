<?php

namespace Modules\Region\Repositories\Region;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Region\Models\Region;

class RegionRepository implements IRegionRepository
{
    /**
     * Get all active regions.
     *
     * @return Collection
     */
    public function getActiveRegions(): Collection
    {
        return Region::where('is_active', true)->get();
    }

    /**
     * Find the first region that contains the given coordinates.
     *
     * @param float $lat
     * @param float $lng
     * @return mixed
     */
    public function findByCoordinates(float $lat, float $lng)
    {
        $point = "POINT($lng $lat)";

        return Region::whereRaw("ST_Contains(polygon, ST_GeomFromText(?))", [$point])
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if a service is available in a given region.
     *
     * @param string $regionId
     * @param string $serviceName
     * @return bool
     */
    public function isServiceAvailable(string $regionId, string $serviceName): bool
    {
        return Region::where('id', $regionId)
            ->where('is_active', true)
            ->whereHas('services', function ($query) use ($serviceName) {
                $query->where('service_name', $serviceName)
                    ->where('is_active', true);
            })
            ->exists();
    }

    /**
     * @param array{name?: string, is_active?: bool} $filters
     */
    public function getAll(array $filters = [], int $page = 1,int $perPage = 15)
    {
        $query = Region::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findById(string $id)
    {
        return Region::find($id);
    }

    /**
     * @param array{name: string, slug: string, polygon: array, timezone?: string, currency_code?: string, is_active?: bool} $data
     */
    public function create(array $data)
    {
        return Region::create($data);
    }

    /**
     * @param array{name?: string, slug?: string, polygon?: array, timezone?: string, currency_code?: string, is_active?: bool} $data
     */
    public function update(string $id, array $data): bool
    {
        $region = $this->findById($id);
        if ($region) {
            return $region->update($data);
        }
        return false;
    }

    public function delete(string $id): bool
    {
        $region = $this->findById($id);
        if ($region) {
            return $region->delete();
        }
        return false;
    }
}
