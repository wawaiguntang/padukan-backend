<?php

namespace Modules\Region\Services\Region;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Region\Models\Region;

interface IRegionService
{
    /**
     * Get a paginated list of regions with optional filters.
     *
     * @param array{name?: string, is_active?: bool} $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllRegions(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Create a new region.
     *
     * @param array{name: string, slug: string, polygon: array, timezone?: string, currency_code?: string, is_active?: bool} $data
     * @return Region
     */
    public function createRegion(array $data): Region;

    /**
     * Find a region by its ID.
     *
     * @param string $regionId
     * @return Region
     */
    public function findRegionById(string $regionId): Region;

    /**
     * Update an existing region.
     *
     * @param string $regionId
     * @param array{name?: string, slug?: string, polygon?: array, timezone?: string, currency_code?: string, is_active?: bool} $data
     * @return Region
     */
    public function updateRegion(string $regionId, array $data): Region;

    /**
     * Delete a region by its ID.
     *
     * @param string $regionId
     * @return bool
     */
    public function deleteRegion(string $regionId): bool;

    /**
     * Toggle the activation status of a region.
     *
     * @param string $regionId
     * @return Region
     */
    public function toggleActivationStatus(string $regionId): Region;

    /**
     * Sync services for a specific region.
     *
     * @param string $regionId
     * @param list<string> $serviceIds
     * @return void
     */
    public function syncServicesForRegion(string $regionId, array $serviceIds): void;
}
