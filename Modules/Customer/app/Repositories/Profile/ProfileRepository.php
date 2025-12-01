<?php

namespace Modules\Customer\Repositories\Profile;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Customer\Enums\GenderEnum;
use Modules\Customer\Models\Profile;
use Modules\Customer\Cache\KeyManager\IKeyManager;

/**
 * Profile Repository Implementation
 *
 * This class handles all profile-related database operations
 * for the customer module with caching support.
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
        $cacheKey = $this->cacheKeyManager::profileById($id);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->find($id);
        });
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
    public function update(string $id, array $data): bool
    {
        $profile = $this->model->find($id); // Don't use cached version for updates

        if (!$profile) {
            return false;
        }

        $result = $profile->update($data);

        if ($result) {
            $profile->refresh();

            // Invalidate and recache profile data
            $this->invalidateProfileCaches($id);
            $this->cacheProfileData($profile);
        }

        return $result;
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
        return $this->model->where('user_id', $userId)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function updateGender(string $id, GenderEnum $gender): bool
    {
        return $this->update($id, ['gender' => $gender]);
    }

    /**
     * Cache profile data in multiple cache keys
     *
     * @param Profile $profile The profile model to cache
     * @return void
     */
    protected function cacheProfileData(Profile $profile): void
    {
        // Cache by user ID (most commonly accessed)
        $this->cache->put($this->cacheKeyManager::profileByUserId($profile->user_id), $profile, $this->cacheTtl);

        // Cache by profile ID
        $this->cache->put($this->cacheKeyManager::profileById($profile->id), $profile, $this->cacheTtl);
    }

    /**
     * Invalidate all cache keys related to a profile
     *
     * @param string $profileId The profile ID
     * @return void
     */
    protected function invalidateProfileCaches(string $profileId): void
    {
        // Get profile data to know which identifiers to invalidate
        $profile = $this->model->find($profileId);

        if ($profile) {
            // Invalidate by user ID
            $this->cache->forget($this->cacheKeyManager::profileByUserId($profile->user_id));

            // Invalidate by profile ID
            $this->cache->forget($this->cacheKeyManager::profileById($profileId));
        }
    }
}
