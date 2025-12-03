<?php

namespace Modules\Setting\Services\Setting;

use Modules\Setting\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

interface ISettingService
{
    /**
     * Get setting by ID
     */
    public function getSettingById(string $id): Setting;

    /**
     * Get setting by key
     */
    public function getSettingByKey(string $key): Setting;

    /**
     * Get setting value by key with type casting
     */
    public function getValue(string $key, $default = null);

    /**
     * Set setting value by key
     */
    public function setValue(string $key, $value, ?string $group = null): Setting;

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
     * Create new setting
     */
    public function createSetting(array $data): Setting;

    /**
     * Update setting
     */
    public function updateSetting(string $id, array $data): bool;

    /**
     * Delete setting
     */
    public function deleteSetting(string $id): bool;

    /**
     * Check if setting exists
     */
    public function settingExists(string $key): bool;

    /**
     * Flatten JSON object into key-value settings
     */
    public function flattenJsonToSettings(array $json, string $prefix = '', ?string $group = null): array;

    /**
     * Reconstruct JSON object from flattened key-value settings
     */
    public function reconstructJsonFromSettings(Collection $settings, string $prefix = ''): array;

    /**
     * Import settings from JSON structure
     */
    public function importFromJson(array $json, ?string $group = null): array;

    /**
     * Export settings to JSON structure
     */
    public function exportToJson(?string $group = null): array;
}
