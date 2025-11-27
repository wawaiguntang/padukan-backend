<?php

namespace Modules\Authorization\Cache\KeyManager;

interface IKeyManager
{
    // ==========================================
    // BUSINESS LOGIC CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for user roles (Business Logic)
     * Used in: RoleRepository::getUserRoles()
     */
    public static function userRoles(string $userId): string;

    /**
     * Generate cache key for user permissions (Business Logic)
     * Used in: PermissionRepository::getUserPermissions()
     */
    public static function userPermissions(string $userId): string;

    /**
     * Generate cache key for role permissions (Business Logic)
     * Used in: PermissionRepository::getRolePermissions()
     */
    public static function rolePermissions(string $roleId): string;

    // ==========================================
    // BASIC DATA CACHE KEYS (Repository Layer)
    // ==========================================

    /**
     * Generate cache key for role lookup by ID (Basic Data)
     * Used in: RoleRepository::findById()
     */
    public static function roleById(string $roleId): string;

    /**
     * Generate cache key for role lookup by slug (Basic Data)
     * Used in: RoleRepository::findBySlug()
     */
    public static function roleBySlug(string $roleSlug): string;

    /**
     * Generate cache key for permission lookup by ID (Basic Data)
     * Used in: PermissionRepository::findById()
     */
    public static function permissionById(string $permissionId): string;

    /**
     * Generate cache key for permission lookup by slug (Basic Data)
     * Used in: PermissionRepository::findBySlug()
     */
    public static function permissionBySlug(string $permissionSlug): string;

    /**
     * Generate cache key for policy setting (Basic Data)
     * Used in: PolicyRepository::getSetting()
     */
    public static function policySetting(string $key): string;

    // ==========================================
    // CACHE INVALIDATION PATTERNS
    // ==========================================

    /**
     * Generate pattern for user-related cache keys (Invalidation)
     * Pattern: authorization:user:{userId}:*
     */
    public static function userPattern(string $userId): string;

    /**
     * Generate pattern for role-related cache keys (Invalidation)
     * Pattern: authorization:role:{roleId}:*
     */
    public static function rolePattern(string $roleId): string;

    /**
     * Generate pattern for permission-related cache keys (Invalidation)
     * Pattern: authorization:permission:{permissionId}:*
     */
    public static function permissionPattern(string $permissionId): string;

    /**
     * Generate pattern for all authorization cache keys (Invalidation)
     * Pattern: authorization:*
     */
    public static function allAuthorizationPattern(): string;
}