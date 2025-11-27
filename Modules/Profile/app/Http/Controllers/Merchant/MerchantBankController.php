<?php

namespace Modules\Profile\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Profile\Services\Merchant\IMerchantService;
use Modules\Profile\Http\Resources\ProfileResponseResource;

/**
 * Merchant Bank Controller
 *
 * Handles merchant bank account management
 */
class MerchantBankController extends Controller
{
    private IMerchantService $merchantService;

    public function __construct(IMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Create merchant bank account
     *
     * @param \Modules\Profile\Http\Requests\CreateMerchantBankRequest $request
     * @return ProfileResponseResource
     */
    public function createBank(\Modules\Profile\Http\Requests\CreateMerchantBankRequest $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        $result = $this->merchantService->createMerchantBank($userId, $data);

        return new ProfileResponseResource($result, 'bank_account_created');
    }

    /**
     * Get merchant bank accounts
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function getBanks(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->merchantService->getMerchantBanks($userId);

        return new ProfileResponseResource($result, 'bank_accounts_retrieved');
    }

    /**
     * Update merchant bank account
     *
     * @param Request $request
     * @param string $bankId
     * @return ProfileResponseResource
     */
    public function updateBank(Request $request, string $bankId): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        $result = $this->merchantService->updateMerchantBank($userId, $bankId, $data);

        return new ProfileResponseResource($result, 'bank_account_updated');
    }

    /**
     * Delete merchant bank account
     *
     * @param Request $request
     * @param string $bankId
     * @return ProfileResponseResource
     */
    public function deleteBank(Request $request, string $bankId): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $deleted = $this->merchantService->deleteMerchantBank($userId, $bankId);

        return new ProfileResponseResource([
            'deleted' => $deleted,
            'bank_id' => $bankId,
        ], 'bank_account_deleted');
    }
}