<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerAddressService;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\AddressNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Delete Address Controller
 *
 * Handles deleting customer addresses
 */
class DeleteAddressController extends Controller
{
    private ICustomerAddressService $addressService;

    public function __construct(ICustomerAddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Delete customer address
     */
    public function deleteAddress(string $addressId): JsonResponse
    {
        try {
            $userId = request()->authenticated_user_id;

            $this->addressService->deleteAddress($userId, $addressId);

            return response()->json([
                'status' => true,
                'message' => __('profile::messages.address_deleted_successfully'),
            ]);
        } catch (ProfileNotFoundException | AddressNotFoundException | UnauthorizedAccessException $e) {
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