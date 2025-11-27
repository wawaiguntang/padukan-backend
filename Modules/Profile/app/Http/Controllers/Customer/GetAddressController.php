<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerAddressService;
use Modules\Profile\Http\Resources\AddressResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\AddressNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Get Address Controller
 *
 * Handles getting specific customer address
 */
class GetAddressController extends Controller
{
    private ICustomerAddressService $addressService;

    public function __construct(ICustomerAddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Get customer address
     */
    public function getAddress(string $addressId): JsonResponse
    {
        try {
            $userId = request()->authenticated_user_id;

            $data = $this->addressService->getAddress($userId, $addressId);

            return response()->json(
                new AddressResponseResource($data, 'profile.address.retrieved_successfully')
            );
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