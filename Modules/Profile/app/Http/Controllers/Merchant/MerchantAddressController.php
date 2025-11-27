<?php

namespace Modules\Profile\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Profile\Services\Merchant\IMerchantService;
use Modules\Profile\Http\Resources\ProfileResponseResource;

/**
 * Merchant Address Controller
 *
 * Handles merchant business address management
 */
class MerchantAddressController extends Controller
{
    private IMerchantService $merchantService;

    public function __construct(IMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Create/update merchant business address
     *
     * @param \Modules\Profile\Http\Requests\CreateMerchantAddressRequest $request
     * @return ProfileResponseResource
     */
    public function createAddress(\Modules\Profile\Http\Requests\CreateMerchantAddressRequest $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        $result = $this->merchantService->createMerchantAddress($userId, $data);

        return new ProfileResponseResource($result, 'business_address_created');
    }

    /**
     * Get merchant business address
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function getAddress(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->merchantService->getMerchantAddress($userId);

        return new ProfileResponseResource($result, 'business_address_retrieved');
    }

    /**
     * Update merchant business address
     *
     * @param \Modules\Profile\Http\Requests\CreateMerchantAddressRequest $request
     * @return ProfileResponseResource
     */
    public function updateAddress(\Modules\Profile\Http\Requests\CreateMerchantAddressRequest $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        $result = $this->merchantService->createMerchantAddress($userId, $data);

        return new ProfileResponseResource($result, 'business_address_updated');
    }
}