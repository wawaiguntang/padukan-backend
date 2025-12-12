<?php

namespace Modules\Tax\Cache;

use Illuminate\Support\Facades\Cache;

/**
 * Tax Cache Manager
 *
 * Handles cache invalidation and monitoring for tax operations.
 * Supports Redis pattern-based deletion.
 */
class TaxCacheManager
{
    /**
     * Invalidate tax entity cache by ID
     */
    public static function invalidateTaxEntity(string $id): void
    {
        Cache::forget(TaxKeyManager::taxById($id));
    }

    /**
     * Invalidate all tax caches using Redis pattern deletion
     */
    public static function invalidateAllTaxes(): void
    {
        $redis = Cache::getRedis();
        $pattern = TaxKeyManager::taxPattern();

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
     * Invalidate assignments by product
     */
    public static function invalidateAssignmentsByProduct(string $productId): void
    {
        Cache::forget(TaxKeyManager::assignmentsByProduct($productId));
    }

    /**
     * Invalidate assignments by category
     */
    public static function invalidateAssignmentsByCategory(string $categoryId): void
    {
        Cache::forget(TaxKeyManager::assignmentsByCategory($categoryId));
    }

    /**
     * Invalidate assignments by region
     */
    public static function invalidateAssignmentsByRegion(string $regionId): void
    {
        Cache::forget(TaxKeyManager::assignmentsByRegion($regionId));
    }

    /**
     * Invalidate assignments by tax group
     */
    public static function invalidateAssignmentsByGroup(string $taxGroupId): void
    {
        Cache::forget(TaxKeyManager::assignmentsByGroup($taxGroupId));
    }

    /**
     * Invalidate tax group cache by ID
     */
    public static function invalidateTaxGroup(string $id): void
    {
        Cache::forget(TaxKeyManager::taxGroupById($id));
        Cache::forget(TaxKeyManager::taxGroupByIdWithDetails($id));
    }

    /**
     * Invalidate tax groups by owner
     */
    public static function invalidateTaxGroupsByOwner(string $ownerId, string $ownerType): void
    {
        Cache::forget(TaxKeyManager::taxGroupsByOwner($ownerId, $ownerType));
        Cache::forget(TaxKeyManager::taxGroupsByOwnerWithDetails($ownerId, $ownerType));
    }

    /**
     * Invalidate all tax groups
     */
    public static function invalidateAllTaxGroups(): void
    {
        Cache::forget(TaxKeyManager::allTaxGroups());
    }

    /**
     * Invalidate tax rate cache by ID
     */
    public static function invalidateTaxRate(string $id): void
    {
        Cache::forget(TaxKeyManager::taxRateById($id));
    }

    /**
     * Invalidate all tax rates
     */
    public static function invalidateAllTaxRates(): void
    {
        Cache::forget(TaxKeyManager::allTaxRates());
    }

    /**
     * Invalidate tax rates by group
     */
    public static function invalidateTaxRatesByGroup(string $taxGroupId): void
    {
        Cache::forget(TaxKeyManager::taxRatesByGroup($taxGroupId));
        Cache::forget(TaxKeyManager::activeTaxRatesByGroup($taxGroupId));
    }

    /**
     * Invalidate tax rates by tax
     */
    public static function invalidateTaxRatesByTax(string $taxId): void
    {
        Cache::forget(TaxKeyManager::taxRatesByTax($taxId));
    }

    /**
     * Invalidate active tax rates by group
     */
    public static function invalidateActiveTaxRatesByGroup(string $taxGroupId): void
    {
        Cache::forget(TaxKeyManager::activeTaxRatesByGroup($taxGroupId));
    }

    /**
     * Invalidate system taxes cache
     */
    public static function invalidateSystemTaxes(): void
    {
        Cache::forget(TaxKeyManager::systemTaxes());
    }

    /**
     * Invalidate merchant taxes cache
     */
    public static function invalidateMerchantTaxes(string $merchantId): void
    {
        Cache::forget(TaxKeyManager::merchantTaxes($merchantId));
    }

    /**
     * Invalidate organization taxes cache
     */
    public static function invalidateOrganizationTaxes(string $organizationId): void
    {
        Cache::forget(TaxKeyManager::organizationTaxes($organizationId));
    }
}
