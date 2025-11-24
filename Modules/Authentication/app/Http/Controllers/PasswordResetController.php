<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authentication\Exceptions\InvalidTokenException;
use Modules\Authentication\Exceptions\UserNotFoundException;
use Modules\Authentication\Http\Requests\ForgotPasswordRequest;
use Modules\Authentication\Http\Requests\ResetPasswordRequest;
use Modules\Authentication\Http\Resources\AuthResource;
use Modules\Authentication\Services\PasswordReset\IPasswordResetService;

/**
 * Password Reset Controller
 *
 * Handles password reset operations
 */
class PasswordResetController extends Controller
{
    /**
     * The password reset service instance
     *
     * @var IPasswordResetService
     */
    protected IPasswordResetService $passwordResetService;

    /**
     * Constructor
     *
     * @param IPasswordResetService $passwordResetService
     */
    public function __construct(IPasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Forgot password - send reset link
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->passwordResetService->forgotPassword(
                $request->input('identifier')
            );

            return response()->json(new AuthResource(null, $result));
        } catch (UserNotFoundException $e) {
            // Don't reveal if user exists or not for security
            return response()->json(new AuthResource(null, 'auth.password_reset.sent'));
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.password_reset.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->passwordResetService->resetPassword(
                $request->input('user_id'),
                $request->input('token'),
                $request->input('password')
            );

            return response()->json(new AuthResource(null, $result));
        } catch (UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (InvalidTokenException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.password_reset.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}