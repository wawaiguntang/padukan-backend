<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Services\Profile\IProfileService;
use Modules\Merchant\Http\Resources\MerchantResource;
use Modules\Merchant\Http\Requests\Merchant\CreateMerchantRequest;

/**
 * Create Merchant Controller
 *
 * Handles creating a new merchant for the authenticated user
 */
class CreateMerchantController
{
    protected IMerchantService $merchantService;
    protected IProfileService $profileService;

    public function __construct(
        IMerchantService $merchantService,
        IProfileService $profileService
    ) {
        $this->merchantService = $merchantService;
        $this->profileService = $profileService;
    }

    /**
     * Create a new merchant
     */
    public function __invoke(CreateMerchantRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;

        // Get user's profile
        $profile = $this->profileService->getProfileByUserId($user->id);

        if (!$profile) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.profile_not_found'),
            ], 404);
        }

        // Check if user can create more merchants
        if (!$this->merchantService->canCreateMerchant($profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.creation_limit_reached'),
            ], 403);
        }

        // Create merchant
        $merchant = $this->merchantService->createMerchant($profile->id, $request->all());

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.merchant.created_successfully'),
            'data' => new MerchantResource($merchant),
        ], 201);
    }
}
