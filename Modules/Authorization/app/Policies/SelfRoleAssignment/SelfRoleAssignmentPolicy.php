<?php

namespace Modules\Authorization\Policies\SelfRoleAssignment;

use Modules\Authorization\Services\Role\IRoleService;
use Modules\Authorization\Repositories\Role\IRoleRepository;
use Modules\Authorization\Repositories\Policy\IPolicyRepository;
use App\Shared\Setting\Services\ISettingService;

class SelfRoleAssignmentPolicy implements ISelfRoleAssignmentPolicy
{
    private IRoleService $roleService;
    private IRoleRepository $roleRepository;
    private ISettingService $settingService;
    private array $policySettings;

    public function __construct(
        IRoleService $roleService,
        IRoleRepository $roleRepository,
        ISettingService $settingService
    ) {
        $this->roleService = $roleService;
        $this->roleRepository = $roleRepository;
        $this->settingService = $settingService;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $this->policySettings = $this->settingService->getSettingByKey('self_role_assignment')['value'] ?? [
            'valid_role_types' => ['customer', 'driver', 'merchant'],
            'allow_multiple_roles' => false
        ];
    }

    /**
     * Evaluate if user can assign a specific role to themselves (main policy function)
     */
    public function evaluate(string $userId, string $roleSlug): bool
    {
        if (!in_array($roleSlug, $this->policySettings['valid_role_types'])) {
            return false;
        }

        // Check if role exists and is active
        $role = $this->roleRepository->findBySlug($roleSlug);
        if (!$role || !$role->is_active) {
            return false;
        }

        if (!$this->policySettings['allow_multiple_roles']) {
            $userRoles = $this->roleService->getUserRoles($userId);
            if (!empty($userRoles)) {
                return false;
            }
        }

        return true;
    }

}
