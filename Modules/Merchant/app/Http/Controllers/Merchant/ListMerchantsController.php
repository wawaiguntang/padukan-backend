<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Services\Profile\IProfileService;
use Modules\Merchant\Http\Resources\MerchantResource;

/**
 * List Merchants Controller
 *
 * Handles listing all merchants for the authenticated user
 */
class ListMerchantsController
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
     * List all merchants for the authenticated user
     */
    public function __invoke(Request $request): JsonResponse
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

        $merchants = $this->merchantService->getMerchantsByProfileId($profile->id);

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.merchant.list_success'),
            'data' => MerchantResource::collection($merchants),
        ]);
    }
}
