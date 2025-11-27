<?php

namespace Modules\Authorization\Services\Policy;

use Modules\Authorization\Models\PolicySetting;
use Modules\Authorization\Repositories\Policy\IPolicyRepository;
use Modules\Authorization\Exceptions\PolicyNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PolicyService implements IPolicyService
{
    private IPolicyRepository $policyRepository;

    public function __construct(IPolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
    }

    /**
     * Get policy setting by key
     *
     * @cache-category Basic Data Cache (Repository Layer)
     * @cache-ttl config('authorization.cache.policy_ttl') - 30 minutes
     * @cache-key authorization:policy:{key}
     * @cache-invalidation When policy setting is updated/deleted
     */
    public function getPolicySetting(string $key): array
    {
        $setting = $this->policyRepository->getSetting($key);

        if (!$setting) {
            throw new PolicyNotFoundException('policy.not_found', ['policy_key' => $key]);
        }
        return $setting;
    }

    /**
     * Update policy setting
     */
    public function updatePolicySetting(string $key, array $settings): bool
    {
        return $this->policyRepository->updateSetting($key, $settings);
    }

    /**
     * Check if policy exists
     */
    public function policyExists(string $key): bool
    {
        return $this->policyRepository->policyExists($key);
    }

    /**
     * Get all active policies
     */
    public function getActivePolicies(): Collection
    {
        return $this->policyRepository->getActivePolicies();
    }

    /**
     * Get policies by pattern
     */
    public function getPoliciesByPattern(string $pattern): Collection
    {
        return $this->policyRepository->getPoliciesByPattern($pattern);
    }

    /**
     * Create new policy setting
     */
    public function createPolicySetting(array $data): PolicySetting
    {
        return DB::transaction(function () use ($data) {
            return $this->policyRepository->createPolicy($data);
        });
    }

    /**
     * Delete policy setting
     */
    public function deletePolicySetting(string $key): bool
    {
        return $this->policyRepository->deletePolicy($key);
    }

    /**
     * Evaluate policy (placeholder for future policy engine)
     * This will be expanded when we implement the actual policy evaluation logic
     */
    public function evaluatePolicy(string $policyKey, array $context): bool
    {
        // For now, just check if policy exists and is active
        return $this->policyExists($policyKey);

        // Future implementation will include:
        // - Load policy class
        // - Execute evaluation logic
        // - Return boolean result
    }
}