<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\FileUpload\IFileUploadService;
use Modules\Profile\Http\Requests\UpdateProfileRequest;
use Modules\Profile\Http\Resources\ProfileResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;
use Modules\Profile\Exceptions\FileUploadException;
use Modules\Profile\Exceptions\InvalidFileException;
use Modules\Profile\Services\Customer\ICustomerService;

class UpdateProfileController extends Controller
{
    private ICustomerService $customerService;

    public function __construct(ICustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Update customer profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $userId = $request->authenticated_user_id;
            $validatedData = $request->validated();

            $data = $this->customerService->updateProfile($userId, $validatedData);

            return response()->json(
                new ProfileResponseResource($data, 'profile.updated_successfully')
            );
        } catch (ProfileNotFoundException | UnauthorizedAccessException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], $e->getCode() ?: 404);
        } catch (FileUploadException | InvalidFileException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessageTranslate(),
            ], $e->getCode() ?: 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('profile::validation.error_occurred'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}