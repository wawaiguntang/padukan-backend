<?php

namespace Modules\Product\Cache\Product;

use Illuminate\Support\Facades\Cache;

/**
 * Product Cache Manager
 *
 * Handles cache invalidation and monitoring for product operations.
 * Supports Redis pattern-based deletion.
 */
class ProductCacheManager
{
    /**
     * Invalidate product entity cache by ID
     */
    public static function invalidateProductEntity(string $id): void
    {
        Cache::forget(ProductKeyManager::productById($id));
    }

    /**
     * Invalidate all product caches using Redis pattern deletion
     */
    public static function invalidateAllProducts(): void
    {
        $redis = Cache::getRedis();
        $pattern = ProductKeyManager::productPattern();

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
     * Invalidate merchant products cache
     */
    public static function invalidateMerchantProducts(string $merchantId): void
    {
        Cache::forget(ProductKeyManager::merchantProducts($merchantId));
    }

    /**
     * Invalidate category products cache
     */
    public static function invalidateCategoryProducts(string $categoryId): void
    {
        Cache::forget(ProductKeyManager::categoryProducts($categoryId));
    }


    /**
     * Smart invalidation for product operations
     */
    public static function invalidateForOperation(string $operation, array $params = []): void
    {
        switch ($operation) {
            case 'create':
                // New product affects merchant lists and all products
                $merchantId = $params['merchant_id'] ?? null;
                if ($merchantId) {
                    self::invalidateMerchantProducts($merchantId);
                }
                self::invalidateAllProducts();
                break;

            case 'update':
                $id = $params['id'] ?? null;
                $data = $params['data'] ?? [];

                if ($id) {
                    self::invalidateProductEntity($id);
                }

                // If category changed, invalidate category caches
                if (isset($data['category_id'])) {
                    self::invalidateAllProducts();
                }

                // If merchant changed, invalidate merchant caches
                if (isset($data['merchant_id'])) {
                    self::invalidateAllProducts();
                }

                // Status changes are handled by expiration status updates
                break;

            case 'delete':
                $id = $params['id'] ?? null;
                $product = $params['product'] ?? null;

                if ($id) {
                    self::invalidateProductEntity($id);
                }

                // Always invalidate lists and related caches on delete
                self::invalidateAllProducts();

                // If product had merchant/category, invalidate those caches
                if ($product && isset($product['merchant_id'])) {
                    self::invalidateMerchantProducts($product['merchant_id']);
                }
                if ($product && isset($product['category_id'])) {
                    self::invalidateCategoryProducts($product['category_id']);
                }
                break;

            default:
                self::invalidateAllProducts();
                break;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        $pattern = ProductKeyManager::productPattern();

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

        // Get memory usage for product keys
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
            'prefix' => ProductKeyManager::getPrefix(),
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
            $pattern = ProductKeyManager::productPattern();
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
     * Clear all product caches (for maintenance)
     */
    public static function clearAll(): int
    {
        $beforeStats = self::getStats();
        self::invalidateAllProducts();
        $afterStats = self::getStats();

        return $beforeStats['total_keys'] - $afterStats['total_keys'];
    }

    /**
     * Warm up common product caches
     */
    public static function warmUp(): array
    {
        // This would typically be called from a service
        // For now, just return available cache keys
        return [
            'merchant_products' => 'Available via ProductKeyManager::merchantProducts($merchantId)',
            'category_products' => 'Available via ProductKeyManager::categoryProducts($categoryId)',
        ];
    }
}
