<?php

namespace App\Shared\Authorization\Repositories;

interface IRoleRepository
{
    /**
     * Find role by slug
     */
    public function findBySlug(string $slug): ?object;

    /**
     * Check if user has role
     */
    public function userHasRole(string $userId, string $roleSlug): bool;

    /**
     * Get user roles
     */
    public function getUserRoles(string $userId): array;
}