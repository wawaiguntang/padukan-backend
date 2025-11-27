<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerAddressService;
use Modules\Profile\Http\Requests\CreateAddressRequest;
use Modules\Profile\Http\Resources\AddressResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Create Address Controller
 *
 * Handles creating customer addresses
 */
class CreateAddressController extends Controller
{
    private ICustomerAddressService $addressService;

    public function __construct(ICustomerAddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Create customer address
     */
    public function createAddress(CreateAddressRequest $request): JsonResponse
    {
        try {
            $userId = $request->authenticated_user_id;
            $validatedData = $request->validated();

            $data = $this->addressService->createAddress($userId, $validatedData);

            return response()->json(
                new AddressResponseResource($data, 'profile.address.created_successfully'),
                201
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