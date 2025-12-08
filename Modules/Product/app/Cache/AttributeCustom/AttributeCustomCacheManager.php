<?php

namespace Modules\Product\Cache\AttributeCustom;

use Illuminate\Support\Facades\Cache;

/**
 * Attribute Custom Cache Manager
 *
 * Handles cache invalidation and monitoring for merchant custom attribute operations.
 * Supports Redis pattern-based deletion.
 */
class AttributeCustomCacheManager
{
    /**
     * Invalidate custom attribute entity cache by ID
     */
    public static function invalidateAttributeEntity(string $id): void
    {
        Cache::forget(AttributeCustomKeyManager::attributeById($id));
    }

    /**
     * Invalidate all custom attributes caches using Redis pattern deletion
     */
    public static function invalidateAllAttributes(): void
    {
        $redis = Cache::getRedis();
        $pattern = AttributeCustomKeyManager::attributePattern();

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
     * Invalidate merchant custom attributes cache
     */
    public static function invalidateMerchantAttributes(string $merchantId): void
    {
        Cache::forget(AttributeCustomKeyManager::merchantAttributes($merchantId));
    }

    /**
     * Invalidate merchant attribute by key cache
     */
    public static function invalidateMerchantAttributeByKey(string $merchantId, string $key): void
    {
        Cache::forget(AttributeCustomKeyManager::merchantAttributeByKey($merchantId, $key));
    }

    /**
     * Smart invalidation for custom attribute operations
     */
    public static function invalidateForOperation(string $operation, array $params = []): void
    {
        switch ($operation) {
            case 'create':
                // New attribute affects merchant's attribute list
                $merchantId = $params['merchant_id'] ?? null;
                if ($merchantId) {
                    self::invalidateMerchantAttributes($merchantId);
                }
                break;

            case 'update':
                $id = $params['id'] ?? null;
                $data = $params['data'] ?? [];
                $oldMerchantId = $params['old_merchant_id'] ?? null;

                if ($id) {
                    self::invalidateAttributeEntity($id);
                }

                // If merchant changed, invalidate both old and new merchant caches
                if (isset($data['merchant_id'])) {
                    if ($oldMerchantId) {
                        self::invalidateMerchantAttributes($oldMerchantId);
                    }
                    self::invalidateMerchantAttributes($data['merchant_id']);
                } else if ($oldMerchantId) {
                    // Same merchant, just invalidate their cache
                    self::invalidateMerchantAttributes($oldMerchantId);
                }

                // If key changed, invalidate specific key cache
                if (isset($data['key']) && $oldMerchantId) {
                    $oldKey = $params['old_key'] ?? null;
                    if ($oldKey) {
                        self::invalidateMerchantAttributeByKey($oldMerchantId, $oldKey);
                    }
                }
                break;

            case 'delete':
                $id = $params['id'] ?? null;
                $attribute = $params['attribute'] ?? null;

                if ($id) {
                    self::invalidateAttributeEntity($id);
                }

                // Always invalidate merchant cache on delete
                if ($attribute && isset($attribute['merchant_id'])) {
                    self::invalidateMerchantAttributes($attribute['merchant_id']);
                    if (isset($attribute['key'])) {
                        self::invalidateMerchantAttributeByKey($attribute['merchant_id'], $attribute['key']);
                    }
                }
                break;

            default:
                self::invalidateAllAttributes();
                break;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        $pattern = AttributeCustomKeyManager::attributePattern();

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

        // Get memory usage for attribute keys
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
            'prefix' => AttributeCustomKeyManager::getPrefix(),
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
            $pattern = AttributeCustomKeyManager::attributePattern();
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
     * Clear all custom attribute caches (for maintenance)
     */
    public static function clearAll(): int
    {
        $beforeStats = self::getStats();
        self::invalidateAllAttributes();
        $afterStats = self::getStats();

        return $beforeStats['total_keys'] - $afterStats['total_keys'];
    }

    /**
     * Warm up common custom attribute caches
     */
    public static function warmUp(): array
    {
        // This would typically be called from a service
        // For now, just return what would be warmed
        return [
            'pattern' => AttributeCustomKeyManager::attributePattern(),
        ];
    }
}
