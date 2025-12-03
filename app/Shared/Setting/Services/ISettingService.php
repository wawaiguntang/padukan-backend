<?php

namespace App\Shared\Setting\Services;

interface ISettingService
{
    /**
     * Get setting by ID
     */
    public function getSettingById(string $id): array;

    /**
     * Get setting by key
     */
    public function getSettingByKey(string $key): array;

    /**
     * Get setting value by key with type casting
     */
    public function getValue(string $key, $default = null);

    /**
     * Set setting value by key
     */
    public function setValue(string $key, $value, ?string $group = null): array;

    /**
     * Get all active settings
     */
    public function getActiveSettings(): array;

    /**
     * Get settings by group
     */
    public function getSettingsByGroup(string $group): array;

    /**
     * Get multiple settings by keys
     */
    public function getSettingsByKeys(array $keys): array;

    /**
     * Create new setting
     */
    public function createSetting(array $data): array;

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
    public function reconstructJsonFromSettings(array $settings, string $prefix = ''): array;

    /**
     * Import settings from JSON structure
     */
    public function importFromJson(array $json, ?string $group = null): array;

    /**
     * Export settings to JSON structure
     */
    public function exportToJson(?string $group = null): array;
}
