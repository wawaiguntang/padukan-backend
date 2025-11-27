<?php

namespace Modules\Profile\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Profile\Services\Driver\IDriverService;
use Modules\Profile\Http\Resources\ProfileResponseResource;
use Modules\Profile\Policies\DocumentOwnershipPolicy;

/**
 * Driver Verification Controller
 *
 * Handles driver verification business logic
 */
class DriverVerificationController extends Controller
{
    private IDriverService $driverService;

    public function __construct(IDriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    /**
     * Request driver verification
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function requestVerification(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->driverService->requestDriverVerification($userId);

        return new ProfileResponseResource($result, 'verification_requested');
    }

    /**
     * Get driver verification status
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function getVerificationStatus(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->driverService->getDriverVerificationStatus($userId);

        return new ProfileResponseResource($result, 'verification_status_retrieved');
    }

    /**
     * Get driver documents for verification
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function getDocuments(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->driverService->getDriverDocumentsForVerification($userId);

        return new ProfileResponseResource($result, 'documents_retrieved');
    }

    /**
     * Get driver document file
     *
     * @param Request $request
     * @param string $documentId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getDocumentFile(Request $request, string $documentId)
    {
        $userId = $request->authenticated_user_id;

        // Check document ownership through policy
        $policy = new DocumentOwnershipPolicy();
        if (!$policy->canAccessDriverDocument($userId, $documentId)) {
            return response()->json([
                'status' => false,
                'message' => __('profile::validation.access_denied')
            ], 403);
        }

        $fileUrl = $this->driverService->getDriverDocumentFileUrl($userId, $documentId);

        return response()->download($fileUrl);
    }

    /**
     * Submit driver documents for verification
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function submitDocuments(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        // Submit all required documents
        $documents = [];

        // SIM document
        if (isset($data['sim_file'])) {
            $documents[] = $this->driverService->createDriverDocument($userId, [
                'type' => 'sim',
                'file' => $data['sim_file'],
                'meta' => [
                    'number' => $data['sim_number'],
                    'expiry_date' => $data['sim_expiry_date']
                ]
            ]);
        }

        // STNK document
        if (isset($data['stnk_file'])) {
            $documents[] = $this->driverService->createDriverDocument($userId, [
                'type' => 'stnk',
                'file' => $data['stnk_file'],
                'meta' => [
                    'number' => $data['stnk_number'],
                    'expiry_date' => $data['stnk_expiry_date']
                ]
            ]);
        }

        // Vehicle photo document
        if (isset($data['vehicle_photo_file'])) {
            $documents[] = $this->driverService->createDriverDocument($userId, [
                'type' => 'vehicle_photo',
                'file' => $data['vehicle_photo_file']
            ]);
        }

        // ID card document
        if (isset($data['id_card_file'])) {
            $documents[] = $this->driverService->createDriverDocument($userId, [
                'type' => 'id_card',
                'file' => $data['id_card_file'],
                'meta' => [
                    'number' => $data['id_card_number'],
                    'expiry_date' => $data['id_card_expiry_date']
                ]
            ]);
        }

        return new ProfileResponseResource([
            'documents' => $documents,
            'message' => 'All required documents submitted successfully'
        ], 'documents_submitted');
    }
}