<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authentication\Exceptions\InvalidCredentialsException;
use Modules\Authentication\Exceptions\UserInactiveException;
use Modules\Authentication\Exceptions\UserNotFoundException;
use Modules\Authentication\Http\Requests\LoginRequest;
use Modules\Authentication\Http\Resources\AuthResource;
use Modules\Authentication\Services\User\IUserService;

/**
 * Login Controller
 *
 * Handles user login
 */
class LoginController extends Controller
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
     * Login user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->login(
                $request->input('identifier'),
                $request->input('password')
            );

            return response()->json(new AuthResource($result, 'auth.login.success'), 200, [], JSON_UNESCAPED_UNICODE);
        } catch (UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 401);
        } catch (UserInactiveException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.login.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}