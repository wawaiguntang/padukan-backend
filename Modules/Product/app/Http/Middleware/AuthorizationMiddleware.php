<?php

namespace Modules\Product\Http\Middleware;

use App\Shared\Authorization\Services\IPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationMiddleware
{
    private IPermissionService $permissionService;

    public function __construct(IPermissionService $permissionService)
    {
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
            if (!$this->permissionService->userHasPermission($request->authenticated_user->id, $permission)) {
                return response()->json([
                    'status' => false,
                    'message' => __('product::middleware.access.denied')
                ], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('product::middleware.access.denied')
            ], 401);
        }
    }
}
