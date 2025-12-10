<?php

namespace Modules\Region\Cache;

use Illuminate\Support\Facades\Cache;

/**
 * Region Cache Manager
 *
 * Handles cache invalidation and monitoring for region operations.
 * Supports Redis pattern-based deletion.
 */
class RegionCacheManager
{
    /**
     * Invalidate region entity cache by ID
     */
    public static function invalidateRegionEntity(string $id): void
    {
        Cache::forget(RegionKeyManager::regionById($id));
    }

    /**
     * Invalidate all region caches using Redis pattern deletion
     */
    public static function invalidateAllRegions(): void
    {
        $redis = Cache::getRedis();
        $pattern = RegionKeyManager::regionPattern();

        $keys = [];
        $cursor = 0;

        do {
            $result = $redis->scan($cursor, [
                'match' => $pattern,
                'count' => 100
            ]);
            $cursor = $result[0];
            $keys = array_merge($keys, $result[1]);
        } while ($cursor != 0);

        if (!empty($keys)) {
            $redis->del($keys);
        }
    }
}
