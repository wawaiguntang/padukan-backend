<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authentication\Http\Requests\RefreshTokenRequest;
use Modules\Authentication\Http\Resources\AuthResource;
use Modules\Authentication\Services\User\IUserService;

/**
 * Token Controller
 *
 * Handles token-related operations
 */
class TokenController extends Controller
{
    /**
     * The user service instance
     *
     * @var IUserService
     */
    protected IUserService $userService;

    /**
     * Constructor
     *
     * @param IUserService $userService
     */
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Refresh access token
     *
     * @param RefreshTokenRequest $request
     * @return JsonResponse
     */
    public function refreshToken(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->refreshToken(
                $request->input('refresh_token')
            );

            if (!$result) {
                return response()->json([
                    'status' => false,
                    'message' => __('authentication::auth.token.invalid_refresh_token'),
                ], 401);
            }

            return response()->json(new AuthResource($result, 'auth.token.refreshed'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.token.refresh_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout user
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            // Get refresh token from request (could be from header or body)
            $refreshToken = request()->input('refresh_token') ?? request()->bearerToken();

            if (!$refreshToken) {
                return response()->json([
                    'status' => false,
                    'message' => __('authentication::auth.token.refresh_token_required'),
                ], 400);
            }

            $result = $this->userService->logout($refreshToken);

            return response()->json(new AuthResource(null, 'auth.logout.success'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.logout.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}