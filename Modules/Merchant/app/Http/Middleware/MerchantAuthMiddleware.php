<?php

namespace Modules\Merchant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Shared\Authentication\Services\IJWTService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Merchant Authentication Middleware
 *
 * This middleware validates JWT tokens for merchant API authentication
 */
class MerchantAuthMiddleware
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
                'message' => __('merchant::middleware.token.missing'),
            ], 401);
        }

        // Validate token
        $user = $this->jwtService->getUserFromToken($token);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::middleware.token.invalid'),
            ], 401);
        }

        // Set authenticated user on request
        $request->merge(['authenticated_user' => $user]);

        return $next($request);
    }
}
