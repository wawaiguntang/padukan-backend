<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authentication\Http\Requests\RegisterRequest;
use Modules\Authentication\Http\Resources\AuthResource;
use Modules\Authentication\Services\User\IUserService;

/**
 * Register Controller
 *
 * Handles user registration
 */
class RegisterController extends Controller
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
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->register(
                $request->input('phone'),
                $request->input('email'),
                $request->input('password')
            );

            return response()->json(new AuthResource($user, 'auth.registration.success'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.registration.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}