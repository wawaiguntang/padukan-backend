<?php

namespace Modules\Setting\Repositories\Setting;

use Modules\Setting\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

interface ISettingRepository
{
    /**
     * Find setting by ID
     */
    public function findById(string $id): ?Setting;

    /**
     * Find setting by key
     */
    public function findByKey(string $key): ?Setting;

    /**
     * Get all active settings
     */
    public function getActiveSettings(): Collection;

    /**
     * Get settings by group
     */
    public function getSettingsByGroup(string $group): Collection;

    /**
     * Get multiple settings by keys
     */
    public function getSettingsByKeys(array $keys): Collection;

    /**
     * Create or update setting
     */
    public function createOrUpdate(array $data): Setting;

    /**
     * Delete setting by key
     */
    public function deleteByKey(string $key): bool;

    /**
     * Check if setting exists by key
     */
    public function existsByKey(string $key): bool;

    /**
     * Get setting value by key with type casting
     */
    public function getValueByKey(string $key, $default = null);

    /**
     * Set setting value by key
     */
    public function setValueByKey(string $key, $value, ?string $group = null): Setting;
}
