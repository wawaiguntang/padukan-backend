<?php

namespace Modules\Product\Cache\UnitConversion;

/**
 * Unit Conversion TTL Manager
 *
 * Centralized management of cache TTL values for global unit conversion caching.
 * Provides consistent TTL values across the application.
 */
class UnitConversionTtlManager
{
    /**
     * Entity-level cache TTL (individual records)
     * Used for: unit conversion by ID
     */
    public const ENTITY = 3600; // 1 hour

    /**
     * Lookup cache TTL (by unit)
     * Used for: unit conversions by unit
     */
    public const LOOKUP = 3600; // 1 hour

    /**
     * List-level cache TTL (collections)
     * Used for: all unit conversions
     */
    public const LIST = 3600; // 1 hour

    /**
     * Get TTL for unit conversion entity cache
     */
    public static function unitConversionEntity(): int
    {
        return self::ENTITY;
    }

    /**
     * Get TTL for unit conversion lookup cache
     */
    public static function unitConversionLookup(): int
    {
        return self::LOOKUP;
    }

    /**
     * Get TTL for unit conversion list cache
     */
    public static function unitConversionList(): int
    {
        return self::LIST;
    }

    /**
     * Get TTL by cache type
     */
    public static function get(string $type): int
    {
        return match ($type) {
            'entity' => self::ENTITY,
            'lookup' => self::LOOKUP,
            'list' => self::LIST,
            default => self::ENTITY
        };
    }

    /**
     * Get all TTL values as array
     */
    public static function all(): array
    {
        return [
            'entity' => self::ENTITY,
            'lookup' => self::LOOKUP,
            'list' => self::LIST,
        ];
    }

    /**
     * Get human-readable TTL descriptions
     */
    public static function descriptions(): array
    {
        return [
            'entity' => 'Entity cache (1 hour) - Individual unit conversion records',
            'lookup' => 'Lookup cache (1 hour) - Unit conversions by unit',
            'list' => 'List cache (1 hour) - All unit conversions collection',
        ];
    }
}
