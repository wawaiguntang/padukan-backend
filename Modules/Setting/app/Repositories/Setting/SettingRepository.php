<?php

namespace Modules\Setting\Repositories\Setting;

use Modules\Setting\Models\Setting;
use Modules\Setting\Cache\KeyManager\IKeyManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Cache\Repository as Cache;

class SettingRepository implements ISettingRepository
{
    private IKeyManager $cacheKeyManager;
    private Cache $cache;

    public function __construct(IKeyManager $cacheKeyManager, Cache $cache)
    {
        $this->cacheKeyManager = $cacheKeyManager;
        $this->cache = $cache;
    }

    /**
     * Find setting by ID
     */
    public function findById(string $id): ?Setting
    {
        $cacheKey = $this->cacheKeyManager::settingById($id);

        return $this->cache->remember($cacheKey, config('setting.cache.lookup_ttl'), function () use ($id) {
            return Setting::find($id);
        });
    }

    /**
     * Find setting by key
     */
    public function findByKey(string $key): ?Setting
    {
        $cacheKey = $this->cacheKeyManager::settingByKey($key);

        return $this->cache->remember($cacheKey, config('setting.cache.lookup_ttl'), function () use ($key) {
            return Setting::byKey($key)->first();
        });
    }

    /**
     * Get all active settings
     */
    public function getActiveSettings(): Collection
    {
        return Setting::active()->get();
    }

    /**
     * Get settings by group
     */
    public function getSettingsByGroup(string $group): Collection
    {
        $cacheKey = $this->cacheKeyManager::settingsByGroup($group);

        return $this->cache->remember($cacheKey, config('setting.cache.group_ttl'), function () use ($group) {
            return Setting::active()->byGroup($group)->get();
        });
    }

    /**
     * Get multiple settings by keys
     */
    public function getSettingsByKeys(array $keys): Collection
    {
        $cacheKey = $this->cacheKeyManager::settingsByKeys($keys);

        return $this->cache->remember($cacheKey, config('setting.cache.keys_ttl'), function () use ($keys) {
            return Setting::active()->whereIn('key', $keys)->get();
        });
    }

    /**
     * Create or update setting
     */
    public function createOrUpdate(array $data): Setting
    {
        return DB::transaction(function () use ($data) {
            $setting = Setting::updateOrCreate(
                ['key' => $data['key']],
                $data
            );

            // Clear related caches
            $this->clearSettingCaches($setting);

            return $setting;
        });
    }

    /**
     * Delete setting by key
     */
    public function deleteByKey(string $key): bool
    {
        $setting = $this->findByKey($key);
        if (!$setting) {
            return false;
        }

        $deleted = $setting->delete();

        if ($deleted) {
            $this->clearSettingCaches($setting);
        }

        return $deleted;
    }

    /**
     * Check if setting exists by key
     */
    public function existsByKey(string $key): bool
    {
        return Setting::byKey($key)->exists();
    }

    /**
     * Get setting value by key with type casting
     */
    public function getValueByKey(string $key, $default = null)
    {
        $setting = $this->findByKey($key);

        return $setting ? $setting->getTypedValue() : $default;
    }

    /**
     * Set setting value by key
     */
    public function setValueByKey(string $key, $value, ?string $group = null): Setting
    {
        $data = ['key' => $key];

        if ($group) {
            $data['group'] = $group;
        }

        $setting = new Setting($data);
        $setting->setTypedValue($value);

        return $this->createOrUpdate(array_merge($data, [
            'value' => $setting->value,
            'type' => $setting->type,
            'is_active' => true,
        ]));
    }

    /**
     * Clear caches related to a setting
     */
    private function clearSettingCaches(Setting $setting): void
    {
        // Clear specific setting caches
        $this->cache->forget($this->cacheKeyManager::settingById($setting->id));
        $this->cache->forget($this->cacheKeyManager::settingByKey($setting->key));

        // Clear group cache if setting has a group
        if ($setting->group) {
            $this->cache->forget($this->cacheKeyManager::settingsByGroup($setting->group));
        }

        // Note: For keys cache, we'd need to track which key combinations are cached
        // This is complex, so we'll let it expire naturally or implement a more sophisticated cache invalidation strategy
    }
}
