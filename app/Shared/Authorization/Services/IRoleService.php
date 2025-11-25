<?php

namespace App\Shared\Authorization\Services;

interface IRoleService
{
    /**
     * Check if user has role
     */
    public function userHasRole(string $userId, string $roleSlug): bool;

    /**
     * Assign role to user
     */
    public function assignRoleToUser(string $userId, string $roleSlug): bool;

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(string $userId, string $roleSlug): bool;

    /**
     * Get user roles
     */
    public function getUserRoles(string $userId): array;
}