<?php

namespace Modules\Setting\Cache\KeyManager;

interface IKeyManager
{
    /**
     * Generate cache key for setting by ID
     */
    public static function settingById(string $id): string;

    /**
     * Generate cache key for setting by key
     */
    public static function settingByKey(string $key): string;

    /**
     * Generate cache key for settings by group
     */
    public static function settingsByGroup(string $group): string;

    /**
     * Generate cache key for multiple settings by keys
     */
    public static function settingsByKeys(array $keys): string;
}
