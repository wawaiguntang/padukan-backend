<?php

namespace Modules\Product\Cache\Category;

/**
 * Category Key Manager
 *
 * Generates cache keys for category-related operations.
 * Only handles key generation, not invalidation.
 */
class CategoryKeyManager
{
    /**
     * Cache key prefix for categories
     */
    private const PREFIX = 'product:category';

    /**
     * Generate cache key for category by ID
     */
    public static function categoryById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }

    /**
     * Generate cache key for category by slug
     */
    public static function categoryBySlug(string $slug): string
    {
        return self::PREFIX . ":slug:{$slug}";
    }

    /**
     * Generate cache key for category roots
     */
    public static function categoryRoots(): string
    {
        return self::PREFIX . ":roots";
    }

    /**
     * Generate cache key for category children
     */
    public static function categoryChildren(string $parentId): string
    {
        return self::PREFIX . ":children:{$parentId}";
    }

    /**
     * Generate cache key for all categories
     */
    public static function allCategories(): string
    {
        return self::PREFIX . ":all";
    }

    /**
     * Generate cache key for category tree
     */
    public static function categoryTree(?string $parentId = null): string
    {
        return $parentId
            ? self::PREFIX . ":tree:parent:{$parentId}"
            : self::PREFIX . ":tree:full";
    }

    /**
     * Generate pattern for category-related cache keys
     */
    public static function categoryPattern(): string
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
