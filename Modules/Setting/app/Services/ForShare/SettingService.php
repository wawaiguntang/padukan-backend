<?php

namespace Modules\Setting\Services\ForShare;

use App\Shared\Setting\Services\ISettingService as ISharedSettingService;
use Modules\Setting\Services\Setting\ISettingService;
use Modules\Setting\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

class SettingService implements ISharedSettingService
{
    private ISettingService $settingService;

    public function __construct(ISettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Convert Setting model to array
     */
    private function settingToArray(Setting $setting): array
    {
        return [
            'id' => $setting->id,
            'key' => $setting->key,
            'value' => $setting->getTypedValue(),
            'type' => $setting->type,
            'group' => $setting->group,
            'is_active' => $setting->is_active,
            'created_at' => $setting->created_at,
            'updated_at' => $setting->updated_at,
        ];
    }

    /**
     * Convert Collection of Setting models to array
     */
    private function settingsToArray(Collection $settings): array
    {
        return $settings->map(function (Setting $setting) {
            return $this->settingToArray($setting);
        })->toArray();
    }

    /**
     * Get setting by ID
     */
    public function getSettingById(string $id): array
    {
        $setting = $this->settingService->getSettingById($id);
        return $this->settingToArray($setting);
    }

    /**
     * Get setting by key
     */
    public function getSettingByKey(string $key): array
    {
        $setting = $this->settingService->getSettingByKey($key);
        return $this->settingToArray($setting);
    }

    /**
     * Get setting value by key with type casting
     */
    public function getValue(string $key, $default = null)
    {
        return $this->settingService->getValue($key, $default);
    }

    /**
     * Set setting value by key
     */
    public function setValue(string $key, $value, ?string $group = null): array
    {
        $setting = $this->settingService->setValue($key, $value, $group);
        return $this->settingToArray($setting);
    }

    /**
     * Get all active settings
     */
    public function getActiveSettings(): array
    {
        $settings = $this->settingService->getActiveSettings();
        return $this->settingsToArray($settings);
    }

    /**
     * Get settings by group
     */
    public function getSettingsByGroup(string $group): array
    {
        $settings = $this->settingService->getSettingsByGroup($group);
        return $this->settingsToArray($settings);
    }

    /**
     * Get multiple settings by keys
     */
    public function getSettingsByKeys(array $keys): array
    {
        $settings = $this->settingService->getSettingsByKeys($keys);
        return $this->settingsToArray($settings);
    }

    /**
     * Create new setting
     */
    public function createSetting(array $data): array
    {
        $setting = $this->settingService->createSetting($data);
        return $this->settingToArray($setting);
    }

    /**
     * Update setting
     */
    public function updateSetting(string $id, array $data): bool
    {
        return $this->settingService->updateSetting($id, $data);
    }

    /**
     * Delete setting
     */
    public function deleteSetting(string $id): bool
    {
        return $this->settingService->deleteSetting($id);
    }

    /**
     * Check if setting exists
     */
    public function settingExists(string $key): bool
    {
        return $this->settingService->settingExists($key);
    }

    /**
     * Flatten JSON object into key-value settings
     */
    public function flattenJsonToSettings(array $json, string $prefix = '', ?string $group = null): array
    {
        return $this->settingService->flattenJsonToSettings($json, $prefix, $group);
    }

    /**
     * Reconstruct JSON object from flattened key-value settings
     */
    public function reconstructJsonFromSettings(array $settings, string $prefix = ''): array
    {
        // Convert array of setting arrays back to Collection for the original method
        $settingModels = collect($settings)->map(function ($settingArray) {
            return new Setting($settingArray);
        });

        return $this->settingService->reconstructJsonFromSettings($settingModels, $prefix);
    }

    /**
     * Import settings from JSON structure
     */
    public function importFromJson(array $json, ?string $group = null): array
    {
        $settings = $this->settingService->importFromJson($json, $group);
        return array_map([$this, 'settingToArray'], $settings);
    }

    /**
     * Export settings to JSON structure
     */
    public function exportToJson(?string $group = null): array
    {
        return $this->settingService->exportToJson($group);
    }
}
