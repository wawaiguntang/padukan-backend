<?php

namespace Modules\Authentication\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Authentication\Services\JWT\IJWTService;
use Symfony\Component\HttpFoundation\Response;

/**
 * JWT Authentication Middleware
 *
 * This middleware validates JWT tokens for API authentication
 */
class JWTMiddleware
{
    /**
     * The JWT service instance
     *
     * @var IJWTService
     */
    protected IJWTService $jwtService;

    /**
     * Constructor
     *
     * @param IJWTService $jwtService
     */
    public function __construct(IJWTService $jwtService)
    {
        $this->jwtService = $jwtService;
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
        // Get token from Authorization header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.token.missing'),
            ], 401);
        }

        // Validate token
        $user = $this->jwtService->getUserFromToken($token);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.token.invalid'),
            ], 401);
        }

        // Set authenticated user on request
        $request->merge(['authenticated_user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}