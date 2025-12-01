<?php

namespace App\Shared\Authorization\Repositories;

use Modules\Authorization\Models\PolicySetting;
use Illuminate\Database\Eloquent\Collection;

interface IPolicyRepository
{
    /**
     * Find policy setting by key
     */
    public function findByKey(string $key): ?PolicySetting;

    /**
     * Get policy setting value by key
     */
    public function getSetting(string $key): ?array;

    /**
     * Get all active policy settings
     */
    public function getActivePolicies(): Collection;

    /**
     * Check if policy exists and is active
     */
    public function policyExists(string $key): bool;

    /**
     * Get policies by partial key match
     */
    public function getPoliciesByPattern(string $pattern): Collection;
}