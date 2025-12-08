<?php

namespace Modules\Product\Cache\UnitConversion;

/**
 * Unit Conversion Key Manager
 *
 * Generates cache keys for global unit conversion operations.
 * Only handles key generation, not invalidation.
 */
class UnitConversionKeyManager
{
    /**
     * Cache key prefix for unit conversions
     */
    private const PREFIX = 'product:unit_conversion';

    /**
     * Generate cache key for unit conversion by ID
     */
    public static function unitConversionById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }

    /**
     * Generate cache key for unit conversions by unit
     */
    public static function unitConversionsByUnit(string $unit): string
    {
        return self::PREFIX . ":unit:{$unit}";
    }

    /**
     * Generate cache key for all unit conversions
     */
    public static function allUnitConversions(): string
    {
        return self::PREFIX . ":all";
    }

    /**
     * Generate pattern for unit conversion-related cache keys
     */
    public static function unitConversionPattern(): string
    {
        return self::PREFIX . ":*";
    }

    /**
     * Get cache key prefix
     */
    public static function getPrefix(): string
    {
        return self::PREFIX;
    }
}