<?php

namespace Modules\Product\Cache\Category;

use Illuminate\Support\Facades\Cache;

/**
 * Category Cache Manager
 *
 * Handles cache invalidation and monitoring for category operations.
 * Supports Redis pattern-based deletion.
 */
class CategoryCacheManager
{
    /**
     * Invalidate category entity cache by ID
     */
    public static function invalidateCategoryEntity(string $id): void
    {
        Cache::forget(CategoryKeyManager::categoryById($id));
    }

    /**
     * Invalidate all category caches using Redis pattern deletion
     */
    public static function invalidateAllCategories(): void
    {
        // Since Redis is the default cache store, we can use Cache facade directly
        $redis = Cache::getRedis();
        $pattern = CategoryKeyManager::categoryPattern();

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
     * Invalidate category children caches
     */
    public static function invalidateCategoryChildren(string $parentId): void
    {
        Cache::forget(CategoryKeyManager::categoryChildren($parentId));

        // Also invalidate tree caches that might include this parent
        self::invalidateCategoryTrees();
    }

    /**
     * Invalidate category roots cache
     */
    public static function invalidateCategoryRoots(): void
    {
        Cache::forget(CategoryKeyManager::categoryRoots());
    }

    /**
     * Invalidate category tree caches
     */
    public static function invalidateCategoryTrees(): void
    {
        $redis = Cache::getRedis();
        $pattern = CategoryKeyManager::getPrefix() . ":tree:*";

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

    /**
     * Smart invalidation for category operations
     */
    public static function invalidateForOperation(string $operation, array $params = []): void
    {
        switch ($operation) {
            case 'create':
                // New category affects roots and all lists
                self::invalidateCategoryRoots();
                self::invalidateAllCategories();
                break;

            case 'update':
                $id = $params['id'] ?? null;
                $data = $params['data'] ?? [];

                if ($id) {
                    self::invalidateCategoryEntity($id);
                }

                // Structural changes require broader invalidation
                if (isset($data['parent_id']) || isset($data['name'])) {
                    self::invalidateAllCategories();
                    self::invalidateCategoryTrees();
                }
                break;

            case 'delete':
                $id = $params['id'] ?? null;
                $category = $params['category'] ?? null;

                if ($id) {
                    self::invalidateCategoryEntity($id);
                }

                // Always invalidate lists and trees on delete
                self::invalidateAllCategories();
                self::invalidateCategoryTrees();

                // If it had children, invalidate their parent references
                if ($category && isset($category['parent_id'])) {
                    self::invalidateCategoryChildren($category['parent_id']);
                }
                break;

            default:
                self::invalidateAllCategories();
                break;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        $pattern = CategoryKeyManager::categoryPattern();

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

        // Get memory usage for category keys
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
            'prefix' => CategoryKeyManager::getPrefix(),
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

            // Test basic connectivity
            $ping = $redis->ping();
            $isConnected = $ping === 'PONG';

            // Test pattern scanning
            $pattern = CategoryKeyManager::categoryPattern();
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
     * Clear all category caches (for maintenance)
     */
    public static function clearAll(): int
    {
        $beforeStats = self::getStats();
        self::invalidateAllCategories();
        $afterStats = self::getStats();

        return $beforeStats['total_keys'] - $afterStats['total_keys'];
    }

    /**
     * Warm up common category caches
     */
    public static function warmUp(): array
    {
        // This would typically be called from a service
        // For now, just return what would be warmed
        return [
            'roots' => CategoryKeyManager::categoryRoots(),
            'all' => CategoryKeyManager::allCategories(),
            'tree' => CategoryKeyManager::categoryTree(),
        ];
    }
}
