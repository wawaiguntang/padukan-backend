<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Http\Requests\ForgotPasswordRequest;
use Modules\Authentication\Http\Requests\LoginRequest;
use Modules\Authentication\Http\Requests\RefreshTokenRequest;
use Modules\Authentication\Http\Requests\RegisterRequest;
use Modules\Authentication\Http\Requests\ResetPasswordRequest;
use Modules\Authentication\Http\Requests\SendOtpRequest;
use Modules\Authentication\Http\Requests\ValidateOtpRequest;
use Modules\Authentication\Http\Resources\AuthResource;
use Modules\Authentication\Services\PasswordReset\IPasswordResetService;
use Modules\Authentication\Services\User\IUserService;
use Modules\Authentication\Services\Verification\IVerificationService;

/**
 * Authentication Controller
 *
 * Handles all authentication-related API endpoints
 */
class AuthenticationController extends Controller
{
    /**
     * The user service instance
     *
     * @var IUserService
     */
    protected IUserService $userService;

    /**
     * The verification service instance
     *
     * @var IVerificationService
     */
    protected IVerificationService $verificationService;

    /**
     * The password reset service instance
     *
     * @var IPasswordResetService
     */
    protected IPasswordResetService $passwordResetService;

    /**
     * Constructor
     *
     * @param IUserService $userService
     * @param IVerificationService $verificationService
     * @param IPasswordResetService $passwordResetService
     */
    public function __construct(
        IUserService $userService,
        IVerificationService $verificationService,
        IPasswordResetService $passwordResetService
    ) {
        $this->userService = $userService;
        $this->verificationService = $verificationService;
        $this->passwordResetService = $passwordResetService;
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
        } catch (\Modules\Authentication\Exceptions\UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (\Modules\Authentication\Exceptions\InvalidCredentialsException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.login.failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send OTP for verification
     *
     * @param SendOtpRequest $request
     * @return JsonResponse
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        try {
            $type = IdentifierType::from($request->input('type'));
            $result = $this->verificationService->sendOtp(
                $request->input('user_id'),
                $type
            );

            return response()->json(new AuthResource(null, $result));
        } catch (\Modules\Authentication\Exceptions\UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (\Modules\Authentication\Exceptions\RateLimitExceededException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 429);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.otp.send_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resend OTP for verification
     *
     * @param SendOtpRequest $request
     * @return JsonResponse
     */
    public function resendOtp(SendOtpRequest $request): JsonResponse
    {
        try {
            $type = IdentifierType::from($request->input('type'));
            $result = $this->verificationService->resendOtp(
                $request->input('user_id'),
                $type
            );

            return response()->json(new AuthResource(null, $result));
        } catch (\Modules\Authentication\Exceptions\UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (\Modules\Authentication\Exceptions\RateLimitExceededException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 429);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.otp.resend_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate OTP
     *
     * @param ValidateOtpRequest $request
     * @return JsonResponse
     */
    public function validateOtp(ValidateOtpRequest $request): JsonResponse
    {
        try {
            $type = IdentifierType::from($request->input('type'));
            $result = $this->verificationService->validateOtp(
                $request->input('user_id'),
                $type,
                $request->input('token')
            );

            return response()->json(new AuthResource(null, 'auth.otp.validated'));
        } catch (\Modules\Authentication\Exceptions\UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (\Modules\Authentication\Exceptions\InvalidTokenException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 400);
        } catch (\Modules\Authentication\Exceptions\OtpExpiredException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('authentication::auth.otp.validation_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
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
        } catch (\Modules\Authentication\Exceptions\UserNotFoundException $e) {
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
        } catch (\Modules\Authentication\Exceptions\UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (\Modules\Authentication\Exceptions\InvalidTokenException $e) {
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

    /**
     * Get authenticated user profile
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = auth('api')->user();

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
