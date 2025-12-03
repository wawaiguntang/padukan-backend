<?php

namespace Modules\Setting\Services\Setting;

use Modules\Setting\Models\Setting;
use Modules\Setting\Repositories\Setting\ISettingRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SettingService implements ISettingService
{
    private ISettingRepository $settingRepository;

    public function __construct(ISettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Get setting by ID
     */
    public function getSettingById(string $id): Setting
    {
        $setting = $this->settingRepository->findById($id);

        if (!$setting) {
            throw new \Exception('Setting not found');
        }

        return $setting;
    }

    /**
     * Get setting by key
     */
    public function getSettingByKey(string $key): Setting
    {
        $setting = $this->settingRepository->findByKey($key);

        if (!$setting) {
            throw new \Exception('Setting not found');
        }

        return $setting;
    }

    /**
     * Get setting value by key with type casting
     */
    public function getValue(string $key, $default = null)
    {
        return $this->settingRepository->getValueByKey($key, $default);
    }

    /**
     * Set setting value by key
     */
    public function setValue(string $key, $value, ?string $group = null): Setting
    {
        return $this->settingRepository->setValueByKey($key, $value, $group);
    }

    /**
     * Get all active settings
     */
    public function getActiveSettings(): Collection
    {
        return $this->settingRepository->getActiveSettings();
    }

    /**
     * Get settings by group
     */
    public function getSettingsByGroup(string $group): Collection
    {
        return $this->settingRepository->getSettingsByGroup($group);
    }

    /**
     * Get multiple settings by keys
     */
    public function getSettingsByKeys(array $keys): Collection
    {
        return $this->settingRepository->getSettingsByKeys($keys);
    }

    /**
     * Create new setting
     */
    public function createSetting(array $data): Setting
    {
        return DB::transaction(function () use ($data) {
            return $this->settingRepository->createOrUpdate($data);
        });
    }

    /**
     * Update setting
     */
    public function updateSetting(string $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $setting = $this->getSettingById($id);
            return $setting->update($data);
        });
    }

    /**
     * Delete setting
     */
    public function deleteSetting(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $setting = $this->getSettingById($id);
            return $this->settingRepository->deleteByKey($setting->key);
        });
    }

    /**
     * Check if setting exists
     */
    public function settingExists(string $key): bool
    {
        return $this->settingRepository->existsByKey($key);
    }

    /**
     * Flatten JSON object into key-value settings
     */
    public function flattenJsonToSettings(array $json, string $prefix = '', ?string $group = null): array
    {
        $settings = [];

        foreach ($json as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value) || is_object($value)) {
                // Recursively flatten nested structures
                $nestedSettings = $this->flattenJsonToSettings(
                    is_object($value) ? (array) $value : $value,
                    $fullKey,
                    $group
                );
                $settings = array_merge($settings, $nestedSettings);
            } else {
                // Create setting data for primitive values
                $setting = new Setting(['key' => $fullKey, 'group' => $group]);
                $setting->setTypedValue($value);

                $settings[] = [
                    'key' => $fullKey,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $group,
                    'is_active' => true,
                ];
            }
        }

        return $settings;
    }

    /**
     * Reconstruct JSON object from flattened key-value settings
     */
    public function reconstructJsonFromSettings(Collection $settings, string $prefix = ''): array
    {
        $result = [];

        foreach ($settings as $setting) {
            $key = $setting->key;

            // Remove prefix if specified
            if ($prefix && str_starts_with($key, $prefix . '.')) {
                $key = substr($key, strlen($prefix) + 1);
            }

            // Skip if key doesn't match prefix
            if ($prefix && !str_starts_with($setting->key, $prefix . '.')) {
                continue;
            }

            $keys = explode('.', $key);
            $current = &$result;

            // Navigate/create nested structure
            foreach ($keys as $i => $k) {
                if ($i === count($keys) - 1) {
                    // Last key, set the value
                    $current[$k] = $setting->getTypedValue();
                } else {
                    // Create nested array if it doesn't exist
                    if (!isset($current[$k]) || !is_array($current[$k])) {
                        $current[$k] = [];
                    }
                    $current = &$current[$k];
                }
            }
        }

        return $result;
    }

    /**
     * Import settings from JSON structure
     */
    public function importFromJson(array $json, ?string $group = null): array
    {
        $settings = $this->flattenJsonToSettings($json, '', $group);
        $createdSettings = [];

        DB::transaction(function () use ($settings, &$createdSettings) {
            foreach ($settings as $settingData) {
                $createdSettings[] = $this->settingRepository->createOrUpdate($settingData);
            }
        });

        return $createdSettings;
    }

    /**
     * Export settings to JSON structure
     */
    public function exportToJson(?string $group = null): array
    {
        $settings = $group
            ? $this->getSettingsByGroup($group)
            : $this->getActiveSettings();

        return $this->reconstructJsonFromSettings($settings);
    }
}
