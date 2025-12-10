<?php

namespace Modules\Region\Services;

use App\Shared\Region\IRegionService;
use Modules\Region\Models\Region;
use Modules\Region\Models\RegionService as RegionServiceModel;
use Illuminate\Support\Facades\Cache;

class RegionService implements IRegionService
{
    /**
     * Get region by coordinates using Ray Casting algorithm (Point in Polygon)
     */
    public function getRegionByCoordinates(float $lat, float $lng): ?array
    {
        // Cache all active regions for calculation
        // In high-scale, use PostGIS 'ST_Contains' instead of PHP loop
        $regions = Cache::remember('active_regions_polygons', 3600, function () {
            return Region::where('is_active', true)->get(['id', 'name', 'polygon', 'timezone', 'currency_code']);
        });

        foreach ($regions as $region) {
            $polygon = $region->polygon;
            if ($this->pointInPolygon($lat, $lng, $polygon)) {
                return $region->toArray();
            }
        }

        return null;
    }

    /**
     * Ray Casting algorithm for Point in Polygon
     */
    private function pointInPolygon($latitude, $longitude, $polygon)
    {
        $c = 0;
        $p1 = $polygon[0];
        $n = count($polygon);

        for ($i = 1; $i <= $n; $i++) {
            $p2 = $polygon[$i % $n];
            if (
                $longitude > min($p1[1], $p2[1])
                && $longitude <= max($p1[1], $p2[1])
                && $latitude <= max($p1[0], $p2[0])
                && $p1[1] != $p2[1]
            ) {
                $xinters = ($longitude - $p1[1]) * ($p2[0] - $p1[0]) / ($p2[1] - $p1[1]) + $p1[0];
                if ($p1[0] == $p2[0] || $latitude <= $xinters) {
                    $c++;
                }
            }
            $p1 = $p2;
        }
        // if the number of intersections is odd, the point is inside the polygon
        return $c % 2 != 0;
    }

    public function isServiceAvailable(string $regionId, string $serviceName): bool
    {
        $cacheKey = "region:{$regionId}:service:{$serviceName}";

        return Cache::remember($cacheKey, 3600, function () use ($regionId, $serviceName) {
            return RegionServiceModel::where('region_id', $regionId)
                ->where('service_name', $serviceName)
                ->where('is_active', true)
                ->exists();
        });
    }

    public function getActiveRegions(): array
    {
        return Region::where('is_active', true)->get()->toArray();
    }
}
