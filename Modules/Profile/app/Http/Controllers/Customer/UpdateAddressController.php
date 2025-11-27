<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerAddressService;
use Modules\Profile\Http\Requests\UpdateAddressRequest;
use Modules\Profile\Http\Resources\AddressResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\AddressNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Update Address Controller
 *
 * Handles updating customer addresses
 */
class UpdateAddressController extends Controller
{
    private ICustomerAddressService $addressService;

    public function __construct(ICustomerAddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Update customer address
     */
    public function updateAddress(UpdateAddressRequest $request, string $addressId): JsonResponse
    {
        try {
            $userId = $request->authenticated_user_id;
            $validatedData = $request->validated();

            $data = $this->addressService->updateAddress($userId, $addressId, $validatedData);

            return response()->json(
                new AddressResponseResource($data, 'profile.address.updated_successfully')
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