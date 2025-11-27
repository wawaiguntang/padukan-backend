<?php

namespace Modules\Authentication\Repositories\User;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Authentication\Enums\UserStatus;
use Modules\Authentication\Models\User;
use Modules\Authentication\Cache\KeyManager\IKeyManager;

/**
 * User Repository Implementation
 *
 * This class handles all user-related database operations
 * for the authentication module with caching support.
 */
class UserRepository implements IUserRepository
{
    /**
     * The User model instance
     *
     * @var User
     */
    protected User $model;

    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * The cache key manager instance
     *
     * @var IKeyManager
     */
    protected IKeyManager $cacheKeyManager;

    /**
     * Cache TTL in seconds (15 minutes - more reasonable for user data)
     *
     * @var int
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     *
     * @param User $model The User model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(User $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findByIdentifier(string $identifier): ?User
    {
        $cacheKey = $this->cacheKeyManager::userByIdentifier($identifier);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($identifier) {
            return $this->model
                ->where('phone', $identifier)
                ->orWhere('email', $identifier)
                ->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): User
    {
        $user = $this->model->create($data);

        // Cache the new user data
        $this->cacheUserData($user);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $user = $this->model->find($id); // Don't use cached version for updates

        if (!$user) {
            return false;
        }

        // Store old identifiers for cache invalidation
        $oldPhone = $user->phone;
        $oldEmail = $user->email;

        $result = $user->update($data);

        if ($result) {
            $user->refresh();

            // Invalidate old identifier caches if they changed
            if (isset($data['phone']) && $data['phone'] !== $oldPhone && $oldPhone) {
                $this->cache->forget($this->cacheKeyManager::userByIdentifier($oldPhone));
            }
            if (isset($data['email']) && $data['email'] !== $oldEmail && $oldEmail) {
                $this->cache->forget($this->cacheKeyManager::userByIdentifier($oldEmail));
            }

            // Invalidate and recache user data
            $this->invalidateUserCaches($id);
            $this->cacheUserData($user);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $user = $this->model->find($id); // Don't use cached version for deletes

        if (!$user) {
            return false;
        }

        $result = $user->delete();

        if ($result) {
            // Invalidate all user caches
            $this->invalidateUserCaches($id);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function existsByIdentifier(string $identifier): bool
    {
        return $this->model
            ->where('phone', $identifier)
            ->orWhere('email', $identifier)
            ->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function updateStatus(string $id, UserStatus $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Cache user data in multiple cache keys
     *
     * @param User $user The user model to cache
     * @return void
     */
    protected function cacheUserData(User $user): void
    {
        // Cache by identifiers only (most commonly accessed)
        if ($user->email) {
            $this->cache->put($this->cacheKeyManager::userByIdentifier($user->email), $user, $this->cacheTtl);
        }
        if ($user->phone) {
            $this->cache->put($this->cacheKeyManager::userByIdentifier($user->phone), $user, $this->cacheTtl);
        }
    }

    /**
     * Invalidate all cache keys related to a user
     *
     * @param string $userId The user ID
     * @return void
     */
    protected function invalidateUserCaches(string $userId): void
    {
        // Get user data to know which identifiers to invalidate
        $user = $this->model->find($userId);

        if ($user) {
            // Invalidate by identifiers
            if ($user->email) {
                $this->cache->forget($this->cacheKeyManager::userByIdentifier($user->email));
            }
            if ($user->phone) {
                $this->cache->forget($this->cacheKeyManager::userByIdentifier($user->phone));
            }
        }
    }
}
