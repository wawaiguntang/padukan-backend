<?php

namespace Modules\Driver\Observers;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Driver\Cache\KeyManager\IKeyManager;
use Modules\Driver\Models\Profile;

/**
 * Profile Model Observer
 *
 * Handles cache management for Profile model events
 */
class ProfileObserver
{
    /**
     * Cache repository instance
     */
    protected Cache $cache;

    /**
     * Cache key manager instance
     */
    protected IKeyManager $keyManager;

    /**
     * Cache TTL in seconds (15 minutes)
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     */
    public function __construct(Cache $cache, IKeyManager $keyManager)
    {
        $this->cache = $cache;
        $this->keyManager = $keyManager;
    }

    /**
     * Handle the Profile "created" event
     */
    public function created(Profile $profile): void
    {
        $this->cacheProfileData($profile);
    }

    /**
     * Handle the Profile "updated" event
     */
    public function updated(Profile $profile): void
    {
        $this->invalidateProfileCaches($profile);
        $this->cacheProfileData($profile);
    }

    /**
     * Handle the Profile "deleted" event
     */
    public function deleted(Profile $profile): void
    {
        $this->invalidateProfileCaches($profile);
    }

    /**
     * Cache profile data
     */
    protected function cacheProfileData(Profile $profile): void
    {
        // Cache by user ID (most commonly accessed)
        $this->cache->put(
            $this->keyManager::profileByUserId($profile->user_id),
            $profile,
            $this->cacheTtl
        );
    }

    /**
     * Invalidate all cache keys related to a profile
     */
    protected function invalidateProfileCaches(Profile $profile): void
    {
        // Invalidate by user ID
        $this->cache->forget($this->keyManager::profileByUserId($profile->user_id));
    }
}
