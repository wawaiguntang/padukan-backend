<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authentication\Http\Resources\AuthResource;

/**
 * Profile Controller
 *
 * Handles user profile operations
 */
class ProfileController extends Controller
{
    /**
     * Get authenticated user profile
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = request()->getUserResolver()();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => __('authentication::auth.user.not_authenticated'),
                ], 401);
            }

            return response()->json(new AuthResource(['user' => $user], 'auth.profile.retrieved'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.profile.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}