<?php

namespace Modules\Authorization\Http\Middleware;

use App\Shared\Authentication\Services\IJWTService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationMiddleware
{
    private IJWTService $jwtService;

    public function __construct(IJWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate JWT token and get user
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => __('authorization::access.denied')
            ], 401);
        }

        try {
            $payload = $this->jwtService->validateAccessToken($token);
            $userId = $payload['sub'] ?? null;

            if (!$userId) {
                return response()->json([
                    'status' => false,
                    'message' => __('authorization::access.denied')
                ], 401);
            }

            // Add user to request for later use
            $request->merge(['authenticated_user_id' => $userId]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authorization::access.denied')
            ], 401);
        }
    }
}
