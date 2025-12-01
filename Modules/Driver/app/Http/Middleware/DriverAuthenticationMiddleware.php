<?php

namespace Modules\Driver\Http\Middleware;

use App\Shared\Authorization\Services\IRoleService;
use Closure;
use Illuminate\Http\Request;
use Modules\Authentication\Services\JWT\IJWTService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Driver Authentication Middleware
 *
 * This middleware validates JWT tokens for driver API authentication
 */
class DriverAuthenticationMiddleware
{
    /**
     * The JWT service instance
     *
     * @var IJWTService
     */
    protected IJWTService $jwtService;

    /**
     * The role service instance
     *
     * @var IRoleService
     */
    protected IRoleService $roleService;

    /**
     * Constructor
     *
     * @param IJWTService $jwtService
     */
    public function __construct(IJWTService $jwtService, IRoleService $roleService)
    {
        $this->jwtService = $jwtService;
        $this->roleService = $roleService;
    }

    /**
     * Handle an incoming request
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => __('driver::auth.token.missing'),
            ], 401);
        }

        $user = $this->jwtService->getUserFromToken($token);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('driver::auth.token.invalid'),
            ], 401);
        }

        $request->merge(['authenticated_user' => $user]);

        return $next($request);
    }
}
