<?php

namespace Modules\Customer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customer Permission Middleware
 *
 * This middleware checks if the authenticated user has the required permissions
 */
class CustomerPermissionMiddleware
{
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
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('customer::auth.user_not_authenticated'),
            ], 401);
        }

        // Check if user has any of the required permissions
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            return response()->json([
                'status' => false,
                'message' => __('customer::auth.insufficient_permissions'),
            ], 403);
        }

        return $next($request);
    }
}
