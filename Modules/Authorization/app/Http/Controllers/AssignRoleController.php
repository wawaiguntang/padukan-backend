<?php

namespace Modules\Authorization\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Authorization\Services\Role\IRoleService;
use Modules\Authorization\Policies\SelfRoleAssignment\ISelfRoleAssignmentPolicy;
use Modules\Authorization\Exceptions\RoleAssignmentNotAllowedException;
use Modules\Authorization\Exceptions\RoleAssignmentFailedException;
use Illuminate\Http\JsonResponse;

class AssignRoleController extends Controller
{
    private IRoleService $roleService;
    private ISelfRoleAssignmentPolicy $roleAssignmentPolicy;

    public function __construct(
        IRoleService $roleService,
        ISelfRoleAssignmentPolicy $roleAssignmentPolicy
    ) {
        $this->roleService = $roleService;
        $this->roleAssignmentPolicy = $roleAssignmentPolicy;
    }

    /**
     * Assign role to current user
     */
    public function assignRole(Request $request, string $roleType): JsonResponse
    {
        $userId = $request->authenticated_user_id;

        // Validate role assignment using policy
        if (!$this->roleAssignmentPolicy->evaluate($userId, $roleType)) {
            throw new RoleAssignmentNotAllowedException();
        }


        $success = $this->roleService->assignRoleToUser($userId, $roleType);
        if (!$success) {
            throw new RoleAssignmentFailedException();
        }


        return response()->json([
            'success' => true,
            'message' => __('authorization::role.role_assigned_successfully'),
            'data' => [
                'user_id' => $userId,
                'role' => $roleType,
            ]
        ]);
    }
}
