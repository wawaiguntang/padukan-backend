<?php

namespace Modules\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Exceptions\InvalidTokenException;
use Modules\Authentication\Exceptions\OtpExpiredException;
use Modules\Authentication\Exceptions\RateLimitExceededException;
use Modules\Authentication\Exceptions\UserNotFoundException;
use Modules\Authentication\Http\Requests\SendOtpRequest;
use Modules\Authentication\Http\Requests\ValidateOtpRequest;
use Modules\Authentication\Http\Resources\AuthResource;
use Modules\Authentication\Services\Verification\IVerificationService;

/**
 * OTP Controller
 *
 * Handles OTP-related operations
 */
class OtpController extends Controller
{
    /**
     * The verification service instance
     *
     * @var IVerificationService
     */
    protected IVerificationService $verificationService;

    /**
     * Constructor
     *
     * @param IVerificationService $verificationService
     */
    public function __construct(IVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
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
            $result = $this->verificationService->sendOtp(
                $request->input('identifier')
            );

            return response()->json(new AuthResource(null, $result));
        } catch (UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (RateLimitExceededException $e) {
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
            $result = $this->verificationService->resendOtp(
                $request->input('identifier')
            );

            return response()->json(new AuthResource(null, $result));
        } catch (UserNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], 404);
        } catch (RateLimitExceededException $e) {
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
                $request->input('identifier'),
                $type,
                $request->input('token')
            );

            return response()->json(new AuthResource(null, 'auth.otp.validated'));
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
        } catch (OtpExpiredException $e) {
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
}