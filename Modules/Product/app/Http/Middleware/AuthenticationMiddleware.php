<?php

namespace Modules\Product\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Shared\Authentication\Services\IJWTService;
use Symfony\Component\HttpFoundation\Response;

/**
 * JWT Authentication Middleware for Product Module
 *
 * This middleware validates JWT tokens for API authentication in the Product module
 */
class AuthenticationMiddleware
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
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => __('product::middleware.token.missing'),
            ], 401);
        }

        $user = $this->jwtService->getUserFromToken($token);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('product::middleware.token.invalid'),
            ], 401);
        }

        $request->merge(['authenticated_user' => $user]);

        return $next($request);
    }
}
