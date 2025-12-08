<?php

namespace Modules\Product\Cache\AttributeCustom;

/**
 * Attribute Custom Key Manager
 *
 * Generates cache keys for merchant custom attribute operations.
 * Only handles key generation, not invalidation.
 */
class AttributeCustomKeyManager
{
    /**
     * Cache key prefix for custom attributes
     */
    private const PREFIX = 'product:attribute_custom';

    /**
     * Generate cache key for custom attribute by ID
     */
    public static function attributeById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }

    /**
     * Generate cache key for merchant's custom attributes
     */
    public static function merchantAttributes(string $merchantId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}";
    }

    /**
     * Generate cache key for merchant attribute by key
     */
    public static function merchantAttributeByKey(string $merchantId, string $key): string
    {
        return self::PREFIX . ":merchant:{$merchantId}:key:{$key}";
    }

    /**
     * Generate pattern for custom attribute-related cache keys
     */
    public static function attributePattern(): string
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
