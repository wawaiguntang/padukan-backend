<?php

namespace Modules\Product\Cache\ProductServiceDetail;

use Illuminate\Support\Facades\Cache;

/**
 * Product Service Detail Cache Manager
 *
 * Handles cache invalidation and monitoring for merchant product service detail operations.
 * Supports Redis pattern-based deletion.
 */
class ProductServiceDetailCacheManager
{
    /**
     * Invalidate service detail entity cache by ID
     */
    public static function invalidateServiceDetailEntity(string $serviceId): void
    {
        Cache::forget(ProductServiceDetailKeyManager::serviceDetailById($serviceId));
    }

    /**
     * Invalidate service detail by product ID cache
     */
    public static function invalidateServiceDetailByProductId(string $productId): void
    {
        Cache::forget(ProductServiceDetailKeyManager::serviceDetailByProductId($productId));
    }

    /**
     * Invalidate merchant service detail by product ID cache
     */
    public static function invalidateMerchantServiceDetailByProductId(string $merchantId, string $productId): void
    {
        Cache::forget(ProductServiceDetailKeyManager::merchantServiceDetailByProductId($merchantId, $productId));
    }

    /**
     * Invalidate all service detail caches using Redis pattern deletion
     */
    public static function invalidateAllServiceDetails(): void
    {
        $redis = Cache::getRedis();
        $pattern = ProductServiceDetailKeyManager::serviceDetailPattern();

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
     * Invalidate all merchant service detail caches
     */
    public static function invalidateMerchantServiceDetails(string $merchantId): void
    {
        $redis = Cache::getRedis();
        $pattern = ProductServiceDetailKeyManager::merchantServiceDetailPattern($merchantId);

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
     * Smart invalidation for service detail operations
     */
    public static function invalidateForOperation(string $operation, array $params = []): void
    {
        switch ($operation) {
            case 'create':
                $productId = $params['product_id'] ?? null;
                $merchantId = $params['merchant_id'] ?? null;

                if ($productId) {
                    self::invalidateServiceDetailByProductId($productId);
                    if ($merchantId) {
                        self::invalidateMerchantServiceDetailByProductId($merchantId, $productId);
                    }
                }
                break;

            case 'update':
                $serviceId = $params['service_id'] ?? null;
                $data = $params['data'] ?? [];
                $oldData = $params['old_data'] ?? [];
                $merchantId = $params['merchant_id'] ?? null;

                if ($serviceId) {
                    self::invalidateServiceDetailEntity($serviceId);
                }

                // Invalidate product caches
                $productId = $data['product_id'] ?? $oldData['product_id'] ?? null;
                $oldProductId = $oldData['product_id'] ?? null;

                if ($productId) {
                    self::invalidateServiceDetailByProductId($productId);
                    if ($merchantId) {
                        self::invalidateMerchantServiceDetailByProductId($merchantId, $productId);
                    }
                }
                if ($oldProductId && $oldProductId !== $productId) {
                    self::invalidateServiceDetailByProductId($oldProductId);
                    if ($merchantId) {
                        self::invalidateMerchantServiceDetailByProductId($merchantId, $oldProductId);
                    }
                }
                break;

            case 'delete':
                $serviceId = $params['service_id'] ?? null;
                $serviceDetail = $params['service_detail'] ?? null;
                $merchantId = $params['merchant_id'] ?? null;

                if ($serviceId) {
                    self::invalidateServiceDetailEntity($serviceId);
                }

                if ($serviceDetail) {
                    $productId = $serviceDetail['product_id'] ?? null;

                    if ($productId) {
                        self::invalidateServiceDetailByProductId($productId);
                        if ($merchantId) {
                            self::invalidateMerchantServiceDetailByProductId($merchantId, $productId);
                        }
                    }
                }
                break;

            default:
                self::invalidateAllServiceDetails();
                break;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        $pattern = ProductServiceDetailKeyManager::serviceDetailPattern();

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

        // Get memory usage for service detail keys
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
            'prefix' => ProductServiceDetailKeyManager::getPrefix(),
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

            $pattern = ProductServiceDetailKeyManager::serviceDetailPattern();
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
     * Clear all service detail caches (for maintenance)
     */
    public static function clearAll(): int
    {
        $beforeStats = self::getStats();
        self::invalidateAllServiceDetails();
        $afterStats = self::getStats();

        return $beforeStats['total_keys'] - $afterStats['total_keys'];
    }

    /**
     * Warm up common service detail caches
     */
    public static function warmUp(): array
    {
        return [
            'pattern' => ProductServiceDetailKeyManager::serviceDetailPattern(),
        ];
    }
}
