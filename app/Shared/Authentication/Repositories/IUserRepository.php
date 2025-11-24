<?php

namespace App\Shared\Repositories;

use Modules\Authentication\Models\User;

/**
 * Interface for User Repository
 *
 * This interface defines the contract for user data operations
 * in the authentication module.
 */
interface IUserRepository
{
    /**
     * Find a user by their identifier (phone or email)
     *
     * @param string $identifier The user's phone number or email address
     * @return User|null The user model if found, null otherwise
     */
    public function findByIdentifier(string $identifier): ?User;

    /**
     * Find a user by their ID
     *
     * @param string $id The user's UUID
     * @return User|null The user model if found, null otherwise
     */
    public function findById(string $id): ?User;

    /**
     * Check if a user exists by their identifier
     *
     * @param string $identifier The user's phone number or email address
     * @return bool True if user exists, false otherwise
     */
    public function existsByIdentifier(string $identifier): bool;
}
