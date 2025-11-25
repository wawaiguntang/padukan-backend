<?php

namespace Modules\Authorization\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Authorization\Http\Resources\CheckRoleResource;
use Modules\Authorization\Services\Role\IRoleService;
use Illuminate\Http\JsonResponse;

class CheckRoleController extends Controller
{
    private IRoleService $roleService;

    public function __construct(IRoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Check current user roles
     */
    public function checkRole(Request $request): JsonResponse
    {
        $userId = $request->authenticated_user_id;
        $appType = $request->query('app_type'); // customer, driver, merchant

        $userRoles = $this->roleService->getUserRoles($userId);

        return response()->json(
            new CheckRoleResource($userRoles, $userId, $appType)
        );
    }
}