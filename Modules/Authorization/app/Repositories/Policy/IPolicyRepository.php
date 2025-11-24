<?php

namespace Modules\Authorization\Repositories\Policy;

use Modules\Authorization\Models\Policy;

/**
 * Interface for Policy Repository
 *
 * This interface defines the contract for policy data operations
 * in the authorization module with comprehensive tree condition support.
 */
interface IPolicyRepository
{
    /**
     * Find a policy by its name
     *
     * @param string $name The policy name
     * @return Policy|null The policy model if found, null otherwise
     */
    public function findByName(string $name): ?Policy;

    /**
     * Find a policy by its ID
     *
     * @param int $id The policy ID
     * @return Policy|null The policy model if found, null otherwise
     */
    public function findById(int $id): ?Policy;

    /**
     * Get all policies
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of policies
     */
    public function getAll();

    /**
     * Get active policies only
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of active policies
     */
    public function getActive();

    /**
     * Get policies by scope
     *
     * @param string $scope The policy scope
     * @return \Illuminate\Database\Eloquent\Collection Collection of policies
     */
    public function getByScope(string $scope);

    /**
     * Get policies by group
     *
     * @param string $group The policy group
     * @return \Illuminate\Database\Eloquent\Collection Collection of policies
     */
    public function getByGroup(string $group);

    /**
     * Get policies by resource
     *
     * @param string $resource The resource name
     * @return \Illuminate\Database\Eloquent\Collection Collection of policies
     */
    public function getByResource(string $resource);

    /**
     * Get policies ordered by group and priority
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of ordered policies
     */
    public function getOrdered();

    /**
     * Create a new policy
     *
     * @param array $data Policy data containing:
     * - name: string - Policy display name
     * - resource?: string - Resource this policy governs
     * - actions?: array - Array of allowed actions
     * - scope?: string - Authentication scope (default: 'default')
     * - group?: string - Policy group (default: 'default')
     * - is_active?: bool - Active status (default: true)
     * - priority?: int - Policy priority (default: 0)
     * - conditions?: array - Complex conditions/rules as JSON
     * - module?: string - Module this policy belongs to
     * - description?: string - Policy description
     * @return Policy The created policy model
     */
    public function create(array $data): Policy;

    /**
     * Update an existing policy
     *
     * @param int $id The policy ID
     * @param array $data Policy data to update
     * @return bool True if update was successful, false otherwise
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a policy
     *
     * @param int $id The policy ID
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete(int $id): bool;

    /**
     * Check if a policy exists by name
     *
     * @param string $name The policy name
     * @return bool True if policy exists, false otherwise
     */
    public function existsByName(string $name): bool;

    /**
     * Evaluate policy conditions against given context
     *
     * @param Policy $policy The policy to evaluate
     * @param array $context Context data for evaluation
     * @return bool True if conditions are met, false otherwise
     */
    public function evaluateConditions(Policy $policy, array $context): bool;

    /**
     * Check if a policy applies to given resource and action
     *
     * @param Policy $policy The policy to check
     * @param string $resource The resource name
     * @param string $action The action name
     * @return bool True if policy applies, false otherwise
     */
    public function appliesTo(Policy $policy, string $resource, string $action): bool;

    /**
     * Get applicable policies for given context
     *
     * @param string $resource The resource name
     * @param string $action The action name
     * @param array $context Additional context data
     * @param string|null $scope Optional scope filter
     * @return \Illuminate\Database\Eloquent\Collection Collection of applicable policies
     */
    public function getApplicablePolicies(string $resource, string $action, array $context = [], ?string $scope = null);

    /**
     * Search policies by name, description, or resource
     *
     * @param string $query The search query
     * @return \Illuminate\Database\Eloquent\Collection Collection of matching policies
     */
    public function search(string $query);

    /**
     * Clone a policy with modifications
     *
     * @param int $sourceId The source policy ID
     * @param array $modifications Modifications to apply
     * @return Policy|null The cloned policy if successful, null otherwise
     */
    public function clonePolicy(int $sourceId, array $modifications): ?Policy;

    /**
     * Bulk update policy priorities within a group
     *
     * @param string $group The policy group
     * @param array $priorityMap Array mapping policy IDs to new priorities
     * @return bool True if bulk update was successful, false otherwise
     */
    public function bulkUpdatePriorities(string $group, array $priorityMap): bool;

    /**
     * Get policy tree structure for a specific group
     *
     * @param string $group The policy group
     * @return array Tree structure of policies
     */
    public function getPolicyTree(string $group): array;

    /**
     * Validate policy conditions structure
     *
     * @param array $conditions The conditions to validate
     * @return bool True if conditions are valid, false otherwise
     */
    public function validateConditions(array $conditions): bool;

    /**
     * Get policies with expired or invalid conditions
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of policies needing review
     */
    public function getPoliciesNeedingReview();
}