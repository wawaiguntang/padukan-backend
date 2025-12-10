<?php

namespace Modules\Region\Repositories\Region;

use Illuminate\Support\Collection;

interface IRegionRepository
{
    /**
     * Get all active regions.
     *
     * @return Collection
     */
    public function getActiveRegions(): Collection;

    /**
     * Find the first region that contains the given coordinates.
     *
     * @param float $lat
     * @param float $lng
     * @return mixed
     */
    public function findByCoordinates(float $lat, float $lng);

    /**
     * Check if a service is available in a given region.
     *
     * @param string $regionId
     * @param string $serviceName
     * @return bool
     */
    public function isServiceAvailable(string $regionId, string $serviceName): bool;

    /**
     * Get a paginated list of regions.
     *
     * @param array{name?: string, is_active?: bool} $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15);

    /**
     * Find a region by its ID.
     *
     * @param string $id
     * @return \Modules\Region\Models\Region|null
     */
    public function findById(string $id);

    /**
     * Create a new region.
     *
     * @param array{name: string, slug: string, polygon: array, timezone?: string, currency_code?: string, is_active?: bool} $data
     * @return \Modules\Region\Models\Region
     */
    public function create(array $data);

    /**
     * Update a region.
     *
     * @param string $id
     * @param array{name?: string, slug?: string, polygon?: array, timezone?: string, currency_code?: string, is_active?: bool} $data
     * @return bool
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a region.
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;
}
