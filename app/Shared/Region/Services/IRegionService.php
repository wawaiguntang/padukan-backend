<?php

namespace App\Shared\Region\Services;

interface IRegionService
{
    /**
     * Get region by coordinates
     *
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @return array|null Region data or null if not found
     */
    public function getRegionByCoordinates(float $lat, float $lng): ?array;

    /**
     * Check if a specific service is available in a region
     *
     * @param string $regionId Region UUID
     * @param string $serviceName Service name (e.g., 'food', 'ride')
     * @return bool
     */
    public function isServiceAvailable(string $regionId, string $serviceName): bool;

    /**
     * Get active regions
     *
     * @return array List of active regions
     */
    public function getActiveRegions(): array;
}
