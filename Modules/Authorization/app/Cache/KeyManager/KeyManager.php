<?php

namespace Modules\Authorization\Cache\KeyManager;

class KeyManager implements IKeyManager
{
    /**
     * Cache key prefix for authorization module
     */
    private const PREFIX = 'authorization';

    // ==========================================
    // BUSINESS LOGIC CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Business Logic Cache
     * Layer: Repository
     * TTL: 1 hour (user_roles_ttl)
     */
    public static function userRoles(string $userId): string
    {
        return self::PREFIX . ":user:{$userId}:roles";
    }

    /**
     * {@inheritDoc}
     * Category: Business Logic Cache
     * Layer: Repository
     * TTL: 1 hour (user_permissions_ttl)
     */
    public static function userPermissions(string $userId): string
    {
        return self::PREFIX . ":user:{$userId}:permissions";
    }

    /**
     * {@inheritDoc}
     * Category: Business Logic Cache
     * Layer: Repository
     * TTL: 1 hour (role_permissions_ttl)
     */
    public static function rolePermissions(string $roleId): string
    {
        return self::PREFIX . ":role:{$roleId}:permissions";
    }

    // ==========================================
    // BASIC DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Basic Data Cache
     * Layer: Repository
     * TTL: 1 hour (lookup_ttl)
     */
    public static function roleById(string $roleId): string
    {
        return self::PREFIX . ":role:id:{$roleId}";
    }

    /**
     * {@inheritDoc}
     * Category: Basic Data Cache
     * Layer: Repository
     * TTL: 1 hour (lookup_ttl)
     */
    public static function roleBySlug(string $roleSlug): string
    {
        return self::PREFIX . ":role:slug:{$roleSlug}";
    }

    /**
     * {@inheritDoc}
     * Category: Basic Data Cache
     * Layer: Repository
     * TTL: 1 hour (lookup_ttl)
     */
    public static function permissionById(string $permissionId): string
    {
        return self::PREFIX . ":permission:id:{$permissionId}";
    }

    /**
     * {@inheritDoc}
     * Category: Basic Data Cache
     * Layer: Repository
     * TTL: 1 hour (lookup_ttl)
     */
    public static function permissionBySlug(string $permissionSlug): string
    {
        return self::PREFIX . ":permission:slug:{$permissionSlug}";
    }

    /**
     * {@inheritDoc}
     * Category: Basic Data Cache
     * Layer: Repository
     * TTL: 30 minutes (policy_ttl)
     */
    public static function policySetting(string $key): string
    {
        return self::PREFIX . ":policy:{$key}";
    }

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all user-related caches
     */
    public static function userPattern(string $userId): string
    {
        return self::PREFIX . ":user:{$userId}:*";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all role-related caches
     */
    public static function rolePattern(string $roleId): string
    {
        return self::PREFIX . ":role:{$roleId}:*";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate all permission-related caches
     */
    public static function permissionPattern(string $permissionId): string
    {
        return self::PREFIX . ":permission:{$permissionId}:*";
    }

    /**
     * {@inheritDoc}
     * Category: Cache Invalidation Pattern
     * Usage: Invalidate ALL authorization caches (dangerous!)
     */
    public static function allAuthorizationPattern(): string
    {
        return self::PREFIX . ":*";
    }
}