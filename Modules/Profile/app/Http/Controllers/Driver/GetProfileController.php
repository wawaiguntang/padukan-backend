<?php

namespace Modules\Profile\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Profile\Services\Driver\IDriverService;
use Modules\Profile\Http\Resources\ProfileResponseResource;

/**
 * Get Driver Profile Controller
 *
 * Handles retrieving driver profile information
 */
class GetProfileController extends Controller
{
    private IDriverService $driverService;

    public function __construct(IDriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Get authenticated driver's profile
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function getProfile(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $data = $this->driverService->getDriverProfile($userId);

        return new ProfileResponseResource($data, 'retrieved_successfully');
    }
}