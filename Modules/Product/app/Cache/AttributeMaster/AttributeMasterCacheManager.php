<?php

namespace Modules\Product\Cache\AttributeMaster;

use Illuminate\Support\Facades\Cache;

/**
 * Attribute Master Cache Manager
 *
 * Handles cache invalidation and monitoring for global attribute master operations.
 * Supports Redis pattern-based deletion.
 */
class AttributeMasterCacheManager
{
    /**
     * Invalidate attribute master entity cache by ID
     */
    public static function invalidateAttributeMasterEntity(string $id): void
    {
        Cache::forget(AttributeMasterKeyManager::attributeMasterById($id));
    }

    /**
     * Invalidate attribute master by key cache
     */
    public static function invalidateAttributeMasterByKey(string $key): void
    {
        Cache::forget(AttributeMasterKeyManager::attributeMasterByKey($key));
    }

    /**
     * Invalidate all attribute masters cache
     */
    public static function invalidateAllAttributeMasters(): void
    {
        Cache::forget(AttributeMasterKeyManager::allAttributeMasters());
    }

    /**
     * Invalidate all attribute master caches using Redis pattern deletion
     */
    public static function invalidateAllAttributeMasterCaches(): void
    {
        $redis = Cache::getRedis();
        $pattern = AttributeMasterKeyManager::attributeMasterPattern();

        // Use Redis SCAN to find all keys matching the pattern
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

        // Delete all matching keys
        if (!empty($keys)) {
            $redis->del($keys);
        }
    }

    /**
     * Smart invalidation for attribute master operations
     */
    public static function invalidateForOperation(string $operation, array $params = []): void
    {
        switch ($operation) {
            case 'create':
                self::invalidateAllAttributeMasters();
                break;

            case 'update':
                $id = $params['id'] ?? null;
                $data = $params['data'] ?? [];
                $oldData = $params['old_data'] ?? [];

                if ($id) {
                    self::invalidateAttributeMasterEntity($id);
                }

                // Invalidate key caches
                $newKey = $data['key'] ?? null;
                $oldKey = $oldData['key'] ?? null;

                if ($newKey && $newKey !== $oldKey) {
                    if ($oldKey) {
                        self::invalidateAttributeMasterByKey($oldKey);
                    }
                    self::invalidateAttributeMasterByKey($newKey);
                }

                self::invalidateAllAttributeMasters();
                break;

            case 'delete':
                $id = $params['id'] ?? null;
                $attributeMaster = $params['attribute_master'] ?? null;

                if ($id) {
                    self::invalidateAttributeMasterEntity($id);
                }

                if ($attributeMaster) {
                    $key = $attributeMaster['key'] ?? null;
                    if ($key) {
                        self::invalidateAttributeMasterByKey($key);
                    }
                }

                self::invalidateAllAttributeMasters();
                break;

            default:
                self::invalidateAllAttributeMasterCaches();
                break;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        $pattern = AttributeMasterKeyManager::attributeMasterPattern();

        $keys = [];
        $cursor = 0;
        $totalKeys = 0;

        do {
            $result = $redis->scan($cursor, [
                'match' => $pattern,
                'count' => 100
            ]);
            $cursor = $result[0];
            $batchKeys = $result[1];
            $keys = array_merge($keys, $batchKeys);
            $totalKeys += count($batchKeys);
        } while ($cursor != 0);

        // Get memory usage for attribute master keys
        $memoryUsage = 0;
        foreach (array_slice($keys, 0, 10) as $key) { // Sample first 10 keys
            $info = $redis->memory('usage', $key);
            if ($info) {
                $memoryUsage += $info;
            }
        }

        return [
            'total_keys' => $totalKeys,
            'sample_keys' => array_slice($keys, 0, 5),
            'estimated_memory_kb' => round($memoryUsage / 1024, 2),
            'prefix' => AttributeMasterKeyManager::getPrefix(),
            'pattern' => $pattern,
        ];
    }

    /**
     * Check cache health
     */
    public static function healthCheck(): array
    {
        try {
            $redis = Cache::getRedis();

            $ping = $redis->ping();
            $isConnected = $ping === 'PONG';

            $pattern = AttributeMasterKeyManager::attributeMasterPattern();
            $cursor = 0;
            $result = $redis->scan($cursor, [
                'match' => $pattern,
                'count' => 1
            ]);
            $canScan = is_array($result) && count($result) >= 2;

            return [
                'healthy' => $isConnected && $canScan,
                'redis_connected' => $isConnected,
                'can_scan_patterns' => $canScan,
                'pattern' => $pattern,
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'error' => $e->getMessage(),
                'redis_connected' => false,
                'can_scan_patterns' => false,
            ];
        }
    }

    /**
     * Clear all attribute master caches (for maintenance)
     */
    public static function clearAll(): int
    {
        $beforeStats = self::getStats();
        self::invalidateAllAttributeMasterCaches();
        $afterStats = self::getStats();

        return $beforeStats['total_keys'] - $afterStats['total_keys'];
    }

    /**
     * Warm up common attribute master caches
     */
    public static function warmUp(): array
    {
        return [
            'pattern' => AttributeMasterKeyManager::attributeMasterPattern(),
        ];
    }
}
