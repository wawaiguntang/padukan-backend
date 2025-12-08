<?php

namespace Modules\Product\Cache\UnitConversion;

use Illuminate\Support\Facades\Cache;

/**
 * Unit Conversion Cache Manager
 *
 * Handles cache invalidation and monitoring for global unit conversion operations.
 * Supports Redis pattern-based deletion.
 */
class UnitConversionCacheManager
{
    /**
     * Invalidate unit conversion entity cache by ID
     */
    public static function invalidateUnitConversionEntity(string $id): void
    {
        Cache::forget(UnitConversionKeyManager::unitConversionById($id));
    }

    /**
     * Invalidate unit conversions by unit cache
     */
    public static function invalidateUnitConversionsByUnit(string $unit): void
    {
        Cache::forget(UnitConversionKeyManager::unitConversionsByUnit($unit));
    }

    /**
     * Invalidate all unit conversions cache
     */
    public static function invalidateAllUnitConversions(): void
    {
        Cache::forget(UnitConversionKeyManager::allUnitConversions());
    }

    /**
     * Invalidate all unit conversion caches using Redis pattern deletion
     */
    public static function invalidateAllUnitConversionCaches(): void
    {
        $redis = Cache::getRedis();
        $pattern = UnitConversionKeyManager::unitConversionPattern();

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
     * Smart invalidation for unit conversion operations
     */
    public static function invalidateForOperation(string $operation, array $params = []): void
    {
        switch ($operation) {
            case 'create':
                $fromUnit = $params['from_unit'] ?? null;
                $toUnit = $params['to_unit'] ?? null;

                if ($fromUnit) {
                    self::invalidateUnitConversionsByUnit($fromUnit);
                }
                if ($toUnit && $toUnit !== $fromUnit) {
                    self::invalidateUnitConversionsByUnit($toUnit);
                }
                self::invalidateAllUnitConversions();
                break;

            case 'update':
                $id = $params['id'] ?? null;
                $data = $params['data'] ?? [];
                $oldData = $params['old_data'] ?? [];

                if ($id) {
                    self::invalidateUnitConversionEntity($id);
                }

                // Invalidate unit caches
                $newFromUnit = $data['from_unit'] ?? null;
                $newToUnit = $data['to_unit'] ?? null;
                $oldFromUnit = $oldData['from_unit'] ?? null;
                $oldToUnit = $oldData['to_unit'] ?? null;

                $unitsToInvalidate = array_unique(array_filter([
                    $newFromUnit,
                    $newToUnit,
                    $oldFromUnit,
                    $oldToUnit
                ]));

                foreach ($unitsToInvalidate as $unit) {
                    self::invalidateUnitConversionsByUnit($unit);
                }

                self::invalidateAllUnitConversions();
                break;

            case 'delete':
                $id = $params['id'] ?? null;
                $unitConversion = $params['unit_conversion'] ?? null;

                if ($id) {
                    self::invalidateUnitConversionEntity($id);
                }

                if ($unitConversion) {
                    $fromUnit = $unitConversion['from_unit'] ?? null;
                    $toUnit = $unitConversion['to_unit'] ?? null;

                    if ($fromUnit) {
                        self::invalidateUnitConversionsByUnit($fromUnit);
                    }
                    if ($toUnit) {
                        self::invalidateUnitConversionsByUnit($toUnit);
                    }
                }

                self::invalidateAllUnitConversions();
                break;

            default:
                self::invalidateAllUnitConversionCaches();
                break;
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        $redis = Cache::getRedis();
        $pattern = UnitConversionKeyManager::unitConversionPattern();

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

        // Get memory usage for unit conversion keys
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
            'prefix' => UnitConversionKeyManager::getPrefix(),
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

            $pattern = UnitConversionKeyManager::unitConversionPattern();
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
     * Clear all unit conversion caches (for maintenance)
     */
    public static function clearAll(): int
    {
        $beforeStats = self::getStats();
        self::invalidateAllUnitConversionCaches();
        $afterStats = self::getStats();

        return $beforeStats['total_keys'] - $afterStats['total_keys'];
    }

    /**
     * Warm up common unit conversion caches
     */
    public static function warmUp(): array
    {
        return [
            'pattern' => UnitConversionKeyManager::unitConversionPattern(),
        ];
    }
}
