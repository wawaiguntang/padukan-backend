<?php

namespace Modules\Setting\Observers;

use Illuminate\Contracts\Cache\Repository as Cache;
use Modules\Setting\Cache\KeyManager\IKeyManager;
use Modules\Setting\Models\Setting;

/**
 * Setting Model Observer
 *
 * Handles cache management for Setting model events
 */
class SettingObserver
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
     * Cache TTL in seconds (30 minutes for settings)
     */
    protected int $cacheTtl = 1800;

    /**
     * Constructor
     */
    public function __construct(Cache $cache, IKeyManager $keyManager)
    {
        $this->cache = $cache;
        $this->keyManager = $keyManager;
    }

    /**
     * Handle the Setting "created" event
     */
    public function created(Setting $setting): void
    {
        $this->invalidateRelatedCaches($setting);
        $this->cacheSettingData($setting);
    }

    /**
     * Handle the Setting "updated" event
     */
    public function updated(Setting $setting): void
    {
        // Get original values before update
        $originalGroup = $setting->getOriginal('group');
        $originalKey = $setting->getOriginal('key');

        // Invalidate caches for both old and new values
        $this->invalidateSettingCaches($setting->id);
        $this->invalidateSettingByKeyCaches($originalKey);

        // If group changed, invalidate old group cache too
        if ($setting->group !== $originalGroup) {
            $this->invalidateGroupCaches($originalGroup);
        }

        // Cache new data
        $this->cacheSettingData($setting);

        // Invalidate group cache for new group
        $this->invalidateGroupCaches($setting->group);
    }

    /**
     * Handle the Setting "deleted" event
     */
    public function deleted(Setting $setting): void
    {
        // Get original values before deletion
        $originalGroup = $setting->getOriginal('group');
        $originalKey = $setting->getOriginal('key');

        // Invalidate all related caches
        $this->invalidateSettingCaches($setting->id);
        $this->invalidateSettingByKeyCaches($originalKey);
        $this->invalidateGroupCaches($originalGroup);
    }

    /**
     * Cache setting data in multiple cache keys
     */
    protected function cacheSettingData(Setting $setting): void
    {
        // Cache by ID (most commonly accessed by ID)
        $this->cache->put(
            $this->keyManager::settingById($setting->id),
            $setting,
            $this->cacheTtl
        );

        // Cache by key
        $this->cache->put(
            $this->keyManager::settingByKey($setting->key),
            $setting,
            $this->cacheTtl
        );

        // Cache by group (for group-based queries)
        $this->cache->put(
            $this->keyManager::settingsByGroup($setting->group),
            $this->getSettingsByGroup($setting->group),
            $this->cacheTtl
        );
    }

    /**
     * Invalidate all cache keys related to a setting by ID
     */
    protected function invalidateSettingCaches(string $settingId): void
    {
        $this->cache->forget($this->keyManager::settingById($settingId));
    }

    /**
     * Invalidate cache keys related to a setting by key
     */
    protected function invalidateSettingByKeyCaches(string $key): void
    {
        $this->cache->forget($this->keyManager::settingByKey($key));
    }

    /**
     * Invalidate group cache
     */
    protected function invalidateGroupCaches(string $group): void
    {
        $this->cache->forget($this->keyManager::settingsByGroup($group));
    }

    /**
     * Invalidate all caches related to this setting
     */
    protected function invalidateRelatedCaches(Setting $setting): void
    {
        $this->invalidateSettingCaches($setting->id);
        $this->invalidateSettingByKeyCaches($setting->key);
        $this->invalidateGroupCaches($setting->group);
    }

    /**
     * Get all settings for a group from database
     */
    protected function getSettingsByGroup(string $group): array
    {
        return Setting::byGroup($group)->active()->get()->toArray();
    }
}
