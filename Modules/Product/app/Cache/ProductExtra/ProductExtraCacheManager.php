<?php

namespace Modules\Product\Cache\ProductExtra;

use Illuminate\Support\Facades\Cache;

/**
 * Product Extra Cache Manager
 *
 * Handles cache invalidation and monitoring for merchant product extra operations.
 * Supports Redis pattern-based deletion.
 */
class ProductExtraCacheManager
{
    /**
     * Invalidate extra entity cache by ID
     */
    public static function invalidateExtraEntity(string $id): void
    {
        Cache::forget(ProductExtraKeyManager::extraById($id));
    }

    /**
     * Invalidate product extras cache
     */
    public static function invalidateProductExtras(string $productId): void
    {
        Cache::forget(ProductExtraKeyManager::productExtras($productId));
    }

    /**
     * Invalidate merchant product extras cache
     */
    public static function invalidateMerchantProductExtras(string $merchantId, string $productId): void
    {
        Cache::forget(ProductExtraKeyManager::merchantProductExtras($merchantId, $productId));
    }

    /**
     * Invalidate all extra caches using Redis pattern deletion
     */
    public static function invalidateAllExtras(): void
    {
        $redis = Cache::getRedis();
        $pattern = ProductExtraKeyManager::extraPattern();

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
     * Invalidate all merchant extra caches
     */
    public static function invalidateMerchantExtras(string $merchantId): void
    {
        $redis = Cache::getRedis();
        $pattern = ProductExtraKeyManager::merchantExtraPattern($merchantId);

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
     * Smart invalidation for extra operations
     */
    public static function invalidateForOperation(string $operation, array $params = []): void
    {
        switch ($operation) {
            case 'create':
                $productId = $params['product_id'] ?? null;
                $merchantId = $params['merchant_id'] ?? null;

                if ($productId) {
                    self::invalidateProductExtras($productId);
                    if ($merchantId) {
                        self::invalidateMerchantProductExtras($merchantId, $productId);
                    }
                }
                break;

            case 'update':
                $id = $params['id'] ?? null;
                $data = $params['data'] ?? [];
                $oldData = $params['old_data'] ?? [];
                $merchantId = $params['merchant_id'] ?? null;

                if ($id) {
                    self::invalidateExtraEntity($id);
                }

                // Invalidate product caches
                $productId = $data['product_id'] ?? $oldData['product_id'] ?? null;
                $oldProductId = $oldData['product_id'] ?? null;

                if ($productId) {
                    self::invalidateProductExtras($productId);
                    if ($merchantId) {
                        self::invalidateMerchantProductExtras($merchantId, $productId);
                    }
                }
                if ($oldProductId && $oldProductId !== $productId) {
                    self::invalidateProductExtras($oldProductId);
                    if ($merchantId) {
                        self::invalidateMerchantProductExtras($merchantId, $oldProductId);
                    }
                }
                break;

            case 'delete':
                $id = $params['id'] ?? null;
                $extra = $params['extra'] ?? null;
                $merchantId = $params['merchant_id'] ?? null;

                if ($id) {
                    self::invalidateExtraEntity($id);
                }

                if ($extra) {
                    $productId = $extra['product_id'] ?? null;

                    if ($productId) {
                        self::invalidateProductExtras($productId);
                        if ($merchantId) {
                            self::invalidateMerchantProductExtras($merchantId, $productId);
                        }
                    }
                }
                break;

            default:
                self::invalidateAllExtras();
                break;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        $pattern = ProductExtraKeyManager::extraPattern();

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

        // Get memory usage for extra keys
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
            'prefix' => ProductExtraKeyManager::getPrefix(),
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

            $pattern = ProductExtraKeyManager::extraPattern();
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
     * Clear all extra caches (for maintenance)
     */
    public static function clearAll(): int
    {
        $beforeStats = self::getStats();
        self::invalidateAllExtras();
        $afterStats = self::getStats();

        return $beforeStats['total_keys'] - $afterStats['total_keys'];
    }

    /**
     * Warm up common extra caches
     */
    public static function warmUp(): array
    {
        return [
            'pattern' => ProductExtraKeyManager::extraPattern(),
        ];
    }
}
