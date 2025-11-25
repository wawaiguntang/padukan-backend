<?php

namespace Modules\Authorization\Policies\SelfRoleAssignment;

use Modules\Authorization\Services\Role\IRoleService;
use Modules\Authorization\Repositories\Role\IRoleRepository;
use Modules\Authorization\Repositories\Policy\IPolicyRepository;

class SelfRoleAssignmentPolicy implements ISelfRoleAssignmentPolicy
{
    private IRoleService $roleService;
    private IRoleRepository $roleRepository;
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(
        IRoleService $roleService,
        IRoleRepository $roleRepository,
        IPolicyRepository $policyRepository
    ) {
        $this->roleService = $roleService;
        $this->roleRepository = $roleRepository;
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('self_role_assignment');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'valid_role_types' => ['customer', 'driver', 'merchant'],
                'allow_multiple_roles' => true,
                'prevent_duplicates' => true,
                'self_assignment_only' => true
            ];
        }
    }

    /**
     * Evaluate if user can assign a specific role to themselves (main policy function)
     */
    public function evaluate(string $userId, string $roleSlug): bool
    {
        // Check if self-assignment is allowed
        if (!$this->policySettings['self_assignment_only']) {
            return false;
        }

        // Validate role type
        if (!in_array($roleSlug, $this->policySettings['valid_role_types'])) {
            return false;
        }

        // Check if role exists and is active
        $role = $this->roleRepository->findBySlug($roleSlug);
        if (!$role || !$role->is_active) {
            return false;
        }

        // Check duplicate prevention
        if ($this->policySettings['prevent_duplicates'] && $this->roleService->userHasRole($userId, $roleSlug)) {
            return false;
        }

        // Check multiple roles allowance
        if (!$this->policySettings['allow_multiple_roles']) {
            $userRoles = $this->roleService->getUserRoles($userId);
            if (!empty($userRoles)) {
                return false;
            }
        }

        return true;
    }

}
