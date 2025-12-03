<?php

namespace Modules\Setting\Cache\KeyManager;

class KeyManager implements IKeyManager
{
    /**
     * Generate cache key for setting by ID
     */
    public static function settingById(string $id): string
    {
        return "setting:id:{$id}";
    }

    /**
     * Generate cache key for setting by key
     */
    public static function settingByKey(string $key): string
    {
        return "setting:key:{$key}";
    }

    /**
     * Generate cache key for settings by group
     */
    public static function settingsByGroup(string $group): string
    {
        return "setting:group:{$group}";
    }

    /**
     * Generate cache key for multiple settings by keys
     */
    public static function settingsByKeys(array $keys): string
    {
        sort($keys);
        $keyString = implode(',', $keys);
        return "setting:keys:{$keyString}";
    }
}
