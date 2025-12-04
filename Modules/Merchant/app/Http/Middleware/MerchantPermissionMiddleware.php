<?php

namespace Modules\Merchant\Http\Middleware;

use App\Shared\Authorization\Services\IPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Merchant Permission Middleware
 *
 * This middleware checks if the authenticated user has the required permissions
 * using the shared authorization service interface
 */
class MerchantPermissionMiddleware
{
    /**
     * The permission service instance
     *
     * @var IPermissionService
     */
    protected IPermissionService $permissionService;

    /**
     * Constructor
     *
     * @param IPermissionService $permissionService
     */
    public function __construct(IPermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$permissions
     * @return Response
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = $request->authenticated_user;

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::middleware.user_not_authenticated'),
            ], 401);
        }

        $hasPermission = false;
        $userId = $user->id;

        foreach ($permissions as $permission) {
            if ($this->permissionService->userHasPermission($userId, $permission)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::middleware.insufficient_permissions'),
            ], 403);
        }

        return $next($request);
    }
}
