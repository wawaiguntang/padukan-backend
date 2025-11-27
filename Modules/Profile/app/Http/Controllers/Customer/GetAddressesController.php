<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerAddressService;
use Modules\Profile\Http\Resources\AddressesResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Get Addresses Controller
 *
 * Handles getting customer addresses
 */
class GetAddressesController extends Controller
{
    private ICustomerAddressService $addressService;

    public function __construct(ICustomerAddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Get customer addresses
     */
    public function getAddresses(): JsonResponse
    {
        try {
            $userId = request()->authenticated_user_id;

            $data = $this->addressService->getAddresses($userId);

            return response()->json(
                new AddressesResponseResource($data, 'profile.addresses.retrieved_successfully')
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