<?php

namespace App\Shared\Authorization\Services;

interface IPolicyService
{
    /**
     * Get policy setting
     */
    public function getPolicySetting(string $key): ?array;

    /**
     * Check if policy exists
     */
    public function policyExists(string $key): bool;

    /**
     * Evaluate policy
     */
    public function evaluatePolicy(string $policyKey, array $context): bool;
}