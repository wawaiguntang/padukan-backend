<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerService;
use Modules\Profile\Http\Resources\ProfileResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

class GetProfileController extends Controller
{
    private ICustomerService $customerService;

    public function __construct(ICustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Get customer profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        try {
            $userId = $request->authenticated_user_id;

            $data = $this->customerService->getProfile($userId);

            return response()->json(
                new ProfileResponseResource($data, 'profile.retrieved_successfully')
            );
        } catch (ProfileNotFoundException | UnauthorizedAccessException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], $e->getCode() ?: 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('profile::validation.error_occurred'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}