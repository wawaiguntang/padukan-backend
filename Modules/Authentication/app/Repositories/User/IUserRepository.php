<?php

namespace Modules\Authentication\Repositories\User;

use Modules\Authentication\Enums\UserStatus;
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
     * Create a new user
     *
     * @param array $data User data containing:
     * - phone?: string - User's phone number (optional)
     * - email?: string - User's email address (optional)
     * - password: string - Hashed password
     * - status?: UserStatus - User status (defaults to PENDING)
     * @return User The created user model
     */
    public function create(array $data): User;

    /**
     * Update an existing user
     *
     * @param string $id The user's UUID
     * @param array $data User data to update (same structure as create)
     * @return bool True if update was successful, false otherwise
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete a user
     *
     * @param string $id The user's UUID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(string $id): bool;

    /**
     * Check if a user exists by their identifier
     *
     * @param string $identifier The user's phone number or email address
     * @return bool True if user exists, false otherwise
     */
    public function existsByIdentifier(string $identifier): bool;

    /**
     * Update a user's status
     *
     * @param string $id The user's UUID
     * @param UserStatus $status The new status
     * @return bool True if update was successful, false otherwise
     */
    public function updateStatus(string $id, UserStatus $status): bool;
}
