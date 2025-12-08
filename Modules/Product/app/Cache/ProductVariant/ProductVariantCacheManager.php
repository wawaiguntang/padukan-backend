<?php

namespace Modules\Product\Cache\ProductVariant;

use Illuminate\Support\Facades\Cache;

/**
 * Product Variant Cache Manager
 *
 * Handles cache invalidation and monitoring for merchant product variant operations.
 * Supports Redis pattern-based deletion.
 */
class ProductVariantCacheManager
{
    /**
     * Invalidate variant entity cache by ID
     */
    public static function invalidateVariantEntity(string $id): void
    {
        Cache::forget(ProductVariantKeyManager::variantById($id));
    }

    /**
     * Invalidate product variants cache
     */
    public static function invalidateProductVariants(string $productId): void
    {
        Cache::forget(ProductVariantKeyManager::productVariants($productId));
    }

    /**
     * Invalidate variant by SKU cache
     */
    public static function invalidateVariantBySku(string $sku): void
    {
        Cache::forget(ProductVariantKeyManager::variantBySku($sku));
    }

    /**
     * Invalidate variant by barcode cache
     */
    public static function invalidateVariantByBarcode(string $barcode): void
    {
        Cache::forget(ProductVariantKeyManager::variantByBarcode($barcode));
    }

    /**
     * Invalidate merchant product variants cache
     */
    public static function invalidateMerchantProductVariants(string $merchantId, string $productId): void
    {
        Cache::forget(ProductVariantKeyManager::merchantProductVariants($merchantId, $productId));
    }

    /**
     * Invalidate merchant variant by SKU cache
     */
    public static function invalidateMerchantVariantBySku(string $merchantId, string $sku): void
    {
        Cache::forget(ProductVariantKeyManager::merchantVariantBySku($merchantId, $sku));
    }

    /**
     * Invalidate merchant variant by barcode cache
     */
    public static function invalidateMerchantVariantByBarcode(string $merchantId, string $barcode): void
    {
        Cache::forget(ProductVariantKeyManager::merchantVariantByBarcode($merchantId, $barcode));
    }

    /**
     * Invalidate all variant caches using Redis pattern deletion
     */
    public static function invalidateAllVariants(): void
    {
        $redis = Cache::getRedis();
        $pattern = ProductVariantKeyManager::variantPattern();

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
     * Invalidate all merchant variant caches
     */
    public static function invalidateMerchantVariants(string $merchantId): void
    {
        $redis = Cache::getRedis();
        $pattern = ProductVariantKeyManager::merchantVariantPattern($merchantId);

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
     * Smart invalidation for variant operations
     */
    public static function invalidateForOperation(string $operation, array $params = []): void
    {
        switch ($operation) {
            case 'create':
                $productId = $params['product_id'] ?? null;
                $sku = $params['sku'] ?? null;
                $barcode = $params['barcode'] ?? null;
                $merchantId = $params['merchant_id'] ?? null;

                if ($productId) {
                    self::invalidateProductVariants($productId);
                    if ($merchantId) {
                        self::invalidateMerchantProductVariants($merchantId, $productId);
                    }
                }
                if ($sku) {
                    self::invalidateVariantBySku($sku);
                    if ($merchantId) {
                        self::invalidateMerchantVariantBySku($merchantId, $sku);
                    }
                }
                if ($barcode) {
                    self::invalidateVariantByBarcode($barcode);
                    if ($merchantId) {
                        self::invalidateMerchantVariantByBarcode($merchantId, $barcode);
                    }
                }
                break;

            case 'update':
                $id = $params['id'] ?? null;
                $data = $params['data'] ?? [];
                $oldData = $params['old_data'] ?? [];
                $merchantId = $params['merchant_id'] ?? null;

                if ($id) {
                    self::invalidateVariantEntity($id);
                }

                // Invalidate product caches
                $productId = $data['product_id'] ?? $oldData['product_id'] ?? null;
                $oldProductId = $oldData['product_id'] ?? null;

                if ($productId) {
                    self::invalidateProductVariants($productId);
                    if ($merchantId) {
                        self::invalidateMerchantProductVariants($merchantId, $productId);
                    }
                }
                if ($oldProductId && $oldProductId !== $productId) {
                    self::invalidateProductVariants($oldProductId);
                    if ($merchantId) {
                        self::invalidateMerchantProductVariants($merchantId, $oldProductId);
                    }
                }

                // Invalidate SKU caches
                $newSku = $data['sku'] ?? null;
                $oldSku = $oldData['sku'] ?? null;

                if ($newSku && $newSku !== $oldSku) {
                    if ($oldSku) {
                        self::invalidateVariantBySku($oldSku);
                        if ($merchantId) {
                            self::invalidateMerchantVariantBySku($merchantId, $oldSku);
                        }
                    }
                    self::invalidateVariantBySku($newSku);
                    if ($merchantId) {
                        self::invalidateMerchantVariantBySku($merchantId, $newSku);
                    }
                }

                // Invalidate barcode caches
                $newBarcode = $data['barcode'] ?? null;
                $oldBarcode = $oldData['barcode'] ?? null;

                if ($newBarcode && $newBarcode !== $oldBarcode) {
                    if ($oldBarcode) {
                        self::invalidateVariantByBarcode($oldBarcode);
                        if ($merchantId) {
                            self::invalidateMerchantVariantByBarcode($merchantId, $oldBarcode);
                        }
                    }
                    self::invalidateVariantByBarcode($newBarcode);
                    if ($merchantId) {
                        self::invalidateMerchantVariantByBarcode($merchantId, $newBarcode);
                    }
                }
                break;

            case 'delete':
                $id = $params['id'] ?? null;
                $variant = $params['variant'] ?? null;
                $merchantId = $params['merchant_id'] ?? null;

                if ($id) {
                    self::invalidateVariantEntity($id);
                }

                if ($variant) {
                    $productId = $variant['product_id'] ?? null;
                    $sku = $variant['sku'] ?? null;
                    $barcode = $variant['barcode'] ?? null;

                    if ($productId) {
                        self::invalidateProductVariants($productId);
                        if ($merchantId) {
                            self::invalidateMerchantProductVariants($merchantId, $productId);
                        }
                    }
                    if ($sku) {
                        self::invalidateVariantBySku($sku);
                        if ($merchantId) {
                            self::invalidateMerchantVariantBySku($merchantId, $sku);
                        }
                    }
                    if ($barcode) {
                        self::invalidateVariantByBarcode($barcode);
                        if ($merchantId) {
                            self::invalidateMerchantVariantByBarcode($merchantId, $barcode);
                        }
                    }
                }
                break;

            default:
                self::invalidateAllVariants();
                break;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        $pattern = ProductVariantKeyManager::variantPattern();

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

        // Get memory usage for variant keys
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
            'prefix' => ProductVariantKeyManager::getPrefix(),
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

            $pattern = ProductVariantKeyManager::variantPattern();
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
     * Clear all variant caches (for maintenance)
     */
    public static function clearAll(): int
    {
        $beforeStats = self::getStats();
        self::invalidateAllVariants();
        $afterStats = self::getStats();

        return $beforeStats['total_keys'] - $afterStats['total_keys'];
    }

    /**
     * Warm up common variant caches
     */
    public static function warmUp(): array
    {
        return [
            'pattern' => ProductVariantKeyManager::variantPattern(),
        ];
    }
}
