<?php

namespace Modules\Authorization\Repositories\Policy;

use Modules\Authorization\Models\PolicySetting;
use Illuminate\Database\Eloquent\Collection;

class PolicyRepository implements IPolicyRepository
{
    /**
     * Find policy setting by key
     */
    public function findByKey(string $key): ?PolicySetting
    {
        return PolicySetting::where('key', $key)->first();
    }

    /**
     * Get policy setting value by key
     */
    public function getSetting(string $key): ?array
    {
        return PolicySetting::get($key);
    }

    /**
     * Update policy setting
     */
    public function updateSetting(string $key, array $settings): bool
    {
        return PolicySetting::updateSetting($key, $settings);
    }

    /**
     * Get all active policy settings
     */
    public function getActivePolicies(): Collection
    {
        return PolicySetting::active()->get();
    }

    /**
     * Check if policy exists and is active
     */
    public function policyExists(string $key): bool
    {
        return PolicySetting::where('key', $key)->where('is_active', true)->exists();
    }

    /**
     * Get policies by partial key match
     */
    public function getPoliciesByPattern(string $pattern): Collection
    {
        return PolicySetting::where('key', 'LIKE', "%{$pattern}%")
                           ->where('is_active', true)
                           ->get();
    }

    /**
     * Create new policy setting
     */
    public function createPolicy(array $data): PolicySetting
    {
        return PolicySetting::create($data);
    }

    /**
     * Delete policy setting
     */
    public function deletePolicy(string $key): bool
    {
        return PolicySetting::where('key', $key)->delete() > 0;
    }
}