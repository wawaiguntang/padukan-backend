<?php

namespace Modules\Authorization\Services\Policy;

use Modules\Authorization\Models\PolicySetting;
use Illuminate\Database\Eloquent\Collection;

interface IPolicyService
{
    /**
     * Get policy setting by key
     */
    public function getPolicySetting(string $key): array;

    /**
     * Update policy setting
     */
    public function updatePolicySetting(string $key, array $settings): bool;

    /**
     * Check if policy exists
     */
    public function policyExists(string $key): bool;

    /**
     * Get all active policies
     */
    public function getActivePolicies(): Collection;

    /**
     * Get policies by pattern
     */
    public function getPoliciesByPattern(string $pattern): Collection;

    /**
     * Create new policy setting
     */
    public function createPolicySetting(array $data): PolicySetting;

    /**
     * Delete policy setting
     */
    public function deletePolicySetting(string $key): bool;

    /**
     * Evaluate policy (placeholder for future policy engine)
     */
    public function evaluatePolicy(string $policyKey, array $context): bool;
}