<?php

namespace Modules\Profile\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Profile\Services\Driver\IDriverService;
use Modules\Profile\Http\Resources\ProfileResponseResource;

/**
 * Update Driver Profile Controller
 *
 * Handles updating driver profile information
 */
class UpdateProfileController extends Controller
{
    private IDriverService $driverService;

    public function __construct(IDriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Update authenticated driver's profile
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function updateProfile(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        $result = $this->driverService->updateDriverProfile($userId, $data);

        return new ProfileResponseResource($result, 'updated_successfully');
    }
}