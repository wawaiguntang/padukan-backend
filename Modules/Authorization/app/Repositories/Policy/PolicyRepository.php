<?php

namespace Modules\Authorization\Repositories\Policy;

use Modules\Authorization\Models\PolicySetting;
use Modules\Authorization\Cache\KeyManager\IKeyManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Cache\Repository as Cache;

class PolicyRepository implements IPolicyRepository
{
    private IKeyManager $cacheKeyManager;
    private Cache $cache;

    public function __construct(IKeyManager $cacheKeyManager, Cache $cache)
    {
        $this->cacheKeyManager = $cacheKeyManager;
        $this->cache = $cache;
    }
    /**
     * Find policy setting by key
     */
    public function findByKey(string $key): ?PolicySetting
    {
        return PolicySetting::where('key', $key)->first();
    }

    /**
     * Get policy setting value by key
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.policy_ttl') - 30 minutes
     * @cache-key authorization:policy:{key}
     */
    public function getSetting(string $key): ?array
    {
        $cacheKey = $this->cacheKeyManager::policySetting($key);

        return $this->cache->remember($cacheKey, config('authorization.cache.policy_ttl'), function () use ($key) {
            return PolicySetting::get($key);
        });
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