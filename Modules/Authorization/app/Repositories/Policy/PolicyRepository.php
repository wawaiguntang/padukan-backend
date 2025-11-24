<?php

namespace Modules\Authorization\Repositories\Policy;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Collection;
use Modules\Authorization\Models\Policy;

/**
 * Policy Repository Implementation
 *
 * This class handles all policy-related database operations
 * for the authorization module with comprehensive tree condition support.
 */
class PolicyRepository implements IPolicyRepository
{
    /**
     * The Policy model instance
     *
     * @var Policy
     */
    protected Policy $model;

    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * Cache TTL in seconds (15 minutes - policies change less frequently)
     *
     * @var int
     */
    protected int $cacheTtl = 900;

    /**
     * Constructor
     *
     * @param Policy $model The Policy model instance
     * @param Cache $cache The cache repository instance
     */
    public function __construct(Policy $model, Cache $cache)
    {
        $this->model = $model;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function findByName(string $name): ?Policy
    {
        $cacheKey = "policy:name:{$name}";

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($name) {
            return $this->model->where('name', $name)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): ?Policy
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll()
    {
        return $this->model->orderBy('group')->orderBy('priority', 'desc')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getActive()
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByScope(string $scope)
    {
        return $this->model->inScope($scope)->ordered()->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByGroup(string $group)
    {
        return $this->model->inGroup($group)->ordered()->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getByResource(string $resource)
    {
        return $this->model
            ->where('resource', $resource)
            ->orWhere('resource', '*')
            ->ordered()
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrdered()
    {
        return $this->model->ordered()->get();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Policy
    {
        // Validate conditions if provided
        if (isset($data['conditions']) && !$this->validateConditions($data['conditions'])) {
            throw new \InvalidArgumentException('Invalid policy conditions structure');
        }

        $policy = $this->model->create($data);

        // Cache the new policy data
        $this->cachePolicyData($policy);

        return $policy;
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data): bool
    {
        $policy = $this->model->find($id);

        if (!$policy) {
            return false;
        }

        // Validate conditions if provided
        if (isset($data['conditions']) && !$this->validateConditions($data['conditions'])) {
            return false;
        }

        // Store old name for cache invalidation
        $oldName = $policy->name;

        $result = $policy->update($data);

        if ($result) {
            $policy->refresh();

            // Invalidate old name cache if it changed
            if (isset($data['name']) && $data['name'] !== $oldName && $oldName) {
                $this->cache->forget("policy:name:{$oldName}");
            }

            // Invalidate and recache policy data
            $this->invalidatePolicyCaches($id);
            $this->cachePolicyData($policy);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        $policy = $this->model->find($id);

        if (!$policy) {
            return false;
        }

        $result = $policy->delete();

        if ($result) {
            // Invalidate all policy caches
            $this->invalidatePolicyCaches($id);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function existsByName(string $name): bool
    {
        return $this->model->where('name', $name)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function evaluateConditions(Policy $policy, array $context): bool
    {
        if (!$policy->conditions) {
            return true; // No conditions means always applicable
        }

        return $this->evaluateConditionTree($policy->conditions, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function appliesTo(Policy $policy, string $resource, string $action): bool
    {
        // Check resource match
        $resourceMatch = $policy->resource === '*' ||
                        $policy->resource === $resource ||
                        str_contains($policy->resource, $resource);

        // Check action match
        $actionMatch = in_array('*', $policy->actions ?? []) ||
                      in_array($action, $policy->actions ?? []);

        return $resourceMatch && $actionMatch && $policy->is_active;
    }

    /**
     * {@inheritDoc}
     */
    public function getApplicablePolicies(string $resource, string $action, array $context = [], ?string $scope = null)
    {
        $query = $this->model->active()
            ->where(function ($q) use ($resource) {
                $q->where('resource', $resource)
                  ->orWhere('resource', '*')
                  ->orWhere('resource', 'like', "%{$resource}%");
            })
            ->where(function ($q) use ($action) {
                $q->whereJsonContains('actions', $action)
                  ->orWhereJsonContains('actions', '*');
            });

        if ($scope) {
            $query->inScope($scope);
        }

        $policies = $query->ordered()->get();

        // Filter by conditions evaluation
        return $policies->filter(function ($policy) use ($context) {
            return $this->evaluateConditions($policy, $context);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function search(string $query)
    {
        return $this->model
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('resource', 'LIKE', "%{$query}%")
            ->ordered()
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function clonePolicy(int $sourceId, array $modifications): ?Policy
    {
        $sourcePolicy = $this->model->find($sourceId);

        if (!$sourcePolicy) {
            return null;
        }

        $newData = array_merge($sourcePolicy->toArray(), $modifications);
        unset($newData['id'], $newData['created_at'], $newData['updated_at']);

        return $this->create($newData);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkUpdatePriorities(string $group, array $priorityMap): bool
    {
        try {
            foreach ($priorityMap as $policyId => $priority) {
                $this->model->where('id', $policyId)->where('group', $group)->update(['priority' => $priority]);
            }

            // Clear group cache
            $this->cache->forget("policies:group:{$group}");

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getPolicyTree(string $group): array
    {
        $policies = $this->getByGroup($group);

        return $this->buildPolicyTree($policies);
    }

    /**
     * {@inheritDoc}
     */
    public function validateConditions(array $conditions): bool
    {
        // Basic validation - can be extended based on your condition schema
        return is_array($conditions) && $this->validateConditionStructure($conditions);
    }

    /**
     * {@inheritDoc}
     */
    public function getPoliciesNeedingReview()
    {
        // This is a placeholder - implement based on your business rules
        // For example, policies with expired dates, invalid conditions, etc.
        return $this->model
            ->where('is_active', true)
            ->where(function ($query) {
                // Add conditions that indicate policies need review
                // This is just an example
                $query->whereNull('conditions')
                      ->orWhere('updated_at', '<', now()->subMonths(6));
            })
            ->get();
    }

    /**
     * Evaluate a tree of conditions recursively
     *
     * @param array $conditions The conditions tree
     * @param array $context The evaluation context
     * @return bool The evaluation result
     */
    protected function evaluateConditionTree(array $conditions, array $context): bool
    {
        // This is a simplified implementation
        // In a real-world scenario, you'd want a more sophisticated rules engine

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                // Nested condition - evaluate recursively
                if (!$this->evaluateConditionTree($value, $context)) {
                    return false;
                }
            } else {
                // Leaf condition - evaluate against context
                if (!$this->evaluateLeafCondition($key, $value, $context)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Evaluate a leaf condition
     *
     * @param string $key The condition key
     * @param mixed $value The expected value
     * @param array $context The evaluation context
     * @return bool The evaluation result
     */
    protected function evaluateLeafCondition(string $key, $value, array $context): bool
    {
        // Simple key-value matching
        // Extend this based on your condition types
        return isset($context[$key]) && $context[$key] === $value;
    }

    /**
     * Build a hierarchical tree structure from policies
     *
     * @param Collection $policies The policies collection
     * @return array The tree structure
     */
    protected function buildPolicyTree(Collection $policies): array
    {
        $tree = [];

        foreach ($policies as $policy) {
            $tree[] = [
                'id' => $policy->id,
                'name' => $policy->name,
                'resource' => $policy->resource,
                'actions' => $policy->actions,
                'priority' => $policy->priority,
                'conditions' => $policy->conditions,
                'children' => [] // Can be extended for hierarchical relationships
            ];
        }

        return $tree;
    }

    /**
     * Validate condition structure recursively
     *
     * @param array $conditions The conditions to validate
     * @return bool True if structure is valid
     */
    protected function validateConditionStructure(array $conditions): bool
    {
        // Basic structure validation
        // Extend this based on your condition schema requirements
        foreach ($conditions as $key => $value) {
            if (!is_string($key)) {
                return false;
            }

            if (is_array($value)) {
                if (!$this->validateConditionStructure($value)) {
                    return false;
                }
            }
            // Add more validation rules as needed
        }

        return true;
    }

    /**
     * Cache policy data in multiple cache keys
     *
     * @param Policy $policy The policy model to cache
     * @return void
     */
    protected function cachePolicyData(Policy $policy): void
    {
        // Cache by name (most commonly accessed)
        $this->cache->put("policy:name:{$policy->name}", $policy, $this->cacheTtl);

        // Cache by group
        $groupKey = "policies:group:{$policy->group}";
        $groupPolicies = $this->cache->get($groupKey, collect());
        $groupPolicies->push($policy);
        $this->cache->put($groupKey, $groupPolicies, $this->cacheTtl);
    }

    /**
     * Invalidate all cache keys related to a policy
     *
     * @param int $policyId The policy ID
     * @return void
     */
    protected function invalidatePolicyCaches(int $policyId): void
    {
        // Get policy data to know which caches to invalidate
        $policy = $this->model->find($policyId);

        if ($policy) {
            // Invalidate by name
            $this->cache->forget("policy:name:{$policy->name}");

            // Invalidate group cache
            $this->cache->forget("policies:group:{$policy->group}");
        }
    }
}