<?php

namespace Modules\Profile\Http\Middleware;

use App\Shared\Authentication\Services\IJWTService;
use App\Shared\Authorization\Services\IPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationMiddleware
{
    private IJWTService $jwtService;
    private IPermissionService $permissionService;

    public function __construct(IJWTService $jwtService, IPermissionService $permissionService)
    {
        $this->jwtService = $jwtService;
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        try {
            // Check if user has the required permission
            if (!$this->permissionService->userHasPermission($request->authenticated_user_id, $permission)) {
                return response()->json([
                    'status' => false,
                    'message' => __('profile::validation.access_denied')
                ], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('profile::validation.access_denied')
            ], 403);
        }
    }
}