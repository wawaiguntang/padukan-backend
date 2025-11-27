<?php

namespace Modules\Profile\Repositories\Profile;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Profile\Models\Profile;
use Modules\Profile\Cache\KeyManager\IKeyManager;

/**
 * Profile Repository Implementation
 *
 * This class handles all profile-related database operations
 * for the profile module with caching support.
 */
class ProfileRepository implements IProfileRepository
{
    /**
     * The Profile model instance
     *
     * @var Profile
     */
    protected Profile $model;

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
     * Cache TTL in seconds (15 minutes - reasonable for profile data)
     *
     * @var int
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     *
     * @param Profile $model The Profile model instance
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     */
    public function __construct(Profile $model, Cache $cache, IKeyManager $cacheKeyManager)
    {
        $this->model = $model;
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
    }

    /**
     * {@inheritDoc}
     */
    public function findByUserId(string $userId): ?Profile
    {
        $cacheKey = $this->cacheKeyManager::profileByUserId($userId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($userId) {
            return $this->model->where('user_id', $userId)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Profile
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Profile
    {
        $profile = $this->model->create($data);

        // Cache the new profile data
        $this->cacheProfileData($profile);

        return $profile;
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): ?Profile
    {
        $profile = $this->model->find($id); // Don't use cached version for updates

        if (!$profile) {
            return null;
        }

        // Store old user_id for cache invalidation
        $oldUserId = $profile->user_id;

        $result = $profile->update($data);

        if ($result) {
            $profile->refresh();

            // Invalidate old user_id caches if it changed
            if (isset($data['user_id']) && $data['user_id'] !== $oldUserId && $oldUserId) {
                $this->cache->forget($this->cacheKeyManager::profileByUserId($oldUserId));
            }

            // Invalidate and recache profile data
            $this->invalidateProfileCaches($id);
            $this->cacheProfileData($profile);

            return $profile;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $profile = $this->model->find($id); // Don't use cached version for deletes

        if (!$profile) {
            return false;
        }

        $result = $profile->delete();

        if ($result) {
            // Invalidate all profile caches
            $this->invalidateProfileCaches($id);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function existsByUserId(string $userId): bool
    {
        $cacheKey = "profile:exists:user_id:{$userId}";

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($userId) {
            return $this->model->where('user_id', $userId)->exists();
        });
    }

    /**
     * Cache profile data in multiple cache keys
     *
     * @param Profile $profile The profile model to cache
     * @return void
     */
    protected function cacheProfileData(Profile $profile): void
    {
        // Cache by user_id (most commonly accessed)
        $this->cache->put($this->cacheKeyManager::profileByUserId($profile->user_id), $profile, $this->cacheTtl);

        // Cache by profile ID for direct access
        $this->cache->put("profile:id:{$profile->id}", $profile, $this->cacheTtl);

        // Cache profile existence check
        $this->cache->put("profile:exists:user_id:{$profile->user_id}", true, $this->cacheTtl);
    }

    /**
     * Invalidate all cache keys related to a profile
     *
     * @param string $profileId The profile ID
     * @return void
     */
    protected function invalidateProfileCaches(string $profileId): void
    {
        // Get profile data to know which user_id to invalidate
        $profile = $this->model->find($profileId);

        if ($profile) {
            // Invalidate all profile-related cache keys
            $this->cache->forget($this->cacheKeyManager::profileByUserId($profile->user_id));
            $this->cache->forget("profile:id:{$profile->id}");
            $this->cache->forget("profile:exists:user_id:{$profile->user_id}");
        }
    }
}