<?php

namespace Modules\Tax\Cache;

/**
 * Tax Key Manager
 *
 * Generates cache keys for tax-related operations.
 */
class TaxKeyManager
{
    /**
     * Cache key prefix for taxes
     */
    private const PREFIX = 'tax';

    /**
     * Generate cache key for tax by ID
     */
    public static function taxById(string $id): string
    {
        return self::PREFIX . ":id:{$id}";
    }

    /**
     * Generate cache key for tax by slug
     */
    public static function taxBySlug(string $slug): string
    {
        return self::PREFIX . ":slug:{$slug}";
    }

    /**
     * Generate cache key for taxes by group
     */
    public static function taxesByGroup(string $groupId): string
    {
        return self::PREFIX . ":group:{$groupId}";
    }

    /**
     * Generate cache key for taxes by owner
     */
    public static function taxesByOwner(string $ownerId, string $ownerType): string
    {
        return self::PREFIX . ":owner:{$ownerId}:{$ownerType}";
    }

    /**
     * Generate cache key for tax group by ID
     */
    public static function taxGroupById(string $id): string
    {
        return self::PREFIX . ":group:id:{$id}";
    }

    /**
     * Generate cache key for tax groups by owner
     */
    public static function taxGroupsByOwner(string $ownerId, string $ownerType): string
    {
        return self::PREFIX . ":group:owner:{$ownerId}:{$ownerType}";
    }

    /**
     * Generate cache key for tax group by ID with details
     */
    public static function taxGroupByIdWithDetails(string $id): string
    {
        return self::PREFIX . ":group:id:{$id}:details";
    }

    /**
     * Generate cache key for tax groups by owner with details
     */
    public static function taxGroupsByOwnerWithDetails(string $ownerId, string $ownerType): string
    {
        return self::PREFIX . ":group:owner:{$ownerId}:{$ownerType}:details";
    }

    /**
     * Generate cache key for all taxes
     */
    public static function allTaxes(): string
    {
        return self::PREFIX . ":all";
    }

    /**
     * Generate cache key for system taxes
     */
    public static function systemTaxes(): string
    {
        return self::PREFIX . ":system";
    }

    /**
     * Generate cache key for merchant taxes
     */
    public static function merchantTaxes(string $merchantId): string
    {
        return self::PREFIX . ":merchant:{$merchantId}";
    }

    /**
     * Generate cache key for organization taxes
     */
    public static function organizationTaxes(string $organizationId): string
    {
        return self::PREFIX . ":organization:{$organizationId}";
    }

    /**
     * Generate cache key for all tax groups
     */
    public static function allTaxGroups(): string
    {
        return self::PREFIX . ":group:all";
    }

    /**
     * Generate cache key for assignments by group
     */
    public static function assignmentsByGroup(string $taxGroupId): string
    {
        return self::PREFIX . ":assignment:group:{$taxGroupId}";
    }

    /**
     * Generate cache key for assignments by product
     */
    public static function assignmentsByProduct(string $productId): string
    {
        return self::PREFIX . ":assignment:product:{$productId}";
    }

    /**
     * Generate cache key for assignments by category
     */
    public static function assignmentsByCategory(string $categoryId): string
    {
        return self::PREFIX . ":assignment:category:{$categoryId}";
    }

    /**
     * Generate cache key for assignments by region
     */
    public static function assignmentsByRegion(string $regionId): string
    {
        return self::PREFIX . ":assignment:region:{$regionId}";
    }

    /**
     * Generate cache key for all tax rates
     */
    public static function allTaxRates(): string
    {
        return self::PREFIX . ":rate:all";
    }

    /**
     * Generate cache key for tax rate by ID
     */
    public static function taxRateById(string $id): string
    {
        return self::PREFIX . ":rate:id:{$id}";
    }

    /**
     * Generate cache key for tax rates by group
     */
    public static function taxRatesByGroup(string $taxGroupId): string
    {
        return self::PREFIX . ":rate:group:{$taxGroupId}";
    }

    /**
     * Generate cache key for tax rates by tax
     */
    public static function taxRatesByTax(string $taxId): string
    {
        return self::PREFIX . ":rate:tax:{$taxId}";
    }

    /**
     * Generate cache key for active tax rates by group
     */
    public static function activeTaxRatesByGroup(string $taxGroupId): string
    {
        return self::PREFIX . ":rate:group:{$taxGroupId}:active";
    }

    /**
     * Generate pattern for tax-related cache keys
     */
    public static function taxPattern(): string
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
