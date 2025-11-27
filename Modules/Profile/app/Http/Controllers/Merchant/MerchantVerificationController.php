<?php

namespace Modules\Profile\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Profile\Services\Merchant\IMerchantService;
use Modules\Profile\Http\Resources\ProfileResponseResource;

/**
 * Merchant Verification Controller
 *
 * Handles merchant verification business logic
 */
class MerchantVerificationController extends Controller
{
    private IMerchantService $merchantService;

    public function __construct(IMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Request merchant verification
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function requestVerification(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->merchantService->requestMerchantVerification($userId);

        return new ProfileResponseResource($result, 'verification_requested');
    }

    /**
     * Get merchant verification status
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function getVerificationStatus(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->merchantService->getMerchantVerificationStatus($userId);

        return new ProfileResponseResource($result, 'verification_status_retrieved');
    }

    /**
     * Submit merchant documents for verification
     *
     * @param \Modules\Profile\Http\Requests\SubmitMerchantDocumentsRequest $request
     * @return ProfileResponseResource
     */
    public function submitDocuments(\Modules\Profile\Http\Requests\SubmitMerchantDocumentsRequest $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;
        $data = $request->validated();

        // Submit all required documents
        $documents = [];

        // ID card document
        if (isset($data['id_card_file'])) {
            $documents[] = $this->merchantService->createMerchantDocument($userId, [
                'type' => 'id_card',
                'file' => $data['id_card_file'],
                'meta' => [
                    'number' => $data['id_card_number'],
                    'expiry_date' => $data['id_card_expiry_date']
                ]
            ]);
        }

        // Store/business license document
        if (isset($data['store_file'])) {
            $documents[] = $this->merchantService->createMerchantDocument($userId, [
                'type' => 'store',
                'file' => $data['store_file'],
                'meta' => [
                    'license_number' => $data['license_number'],
                    'expiry_date' => $data['license_expiry_date']
                ]
            ]);
        }

        return new ProfileResponseResource([
            'documents' => $documents,
            'message' => 'All required documents submitted successfully'
        ], 'documents_submitted');
    }

    /**
     * Get merchant documents for verification status check
     *
     * @param Request $request
     * @return ProfileResponseResource
     */
    public function getDocuments(Request $request): ProfileResponseResource
    {
        $userId = $request->authenticated_user_id;

        $result = $this->merchantService->getMerchantDocumentsForVerification($userId);

        return new ProfileResponseResource($result, 'documents_retrieved');
    }

    /**
     * Get merchant document file URL
     *
     * @param Request $request
     * @param string $documentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocumentFile(Request $request, string $documentId)
    {
        $userId = $request->authenticated_user_id;

        // Check document ownership through service
        if (!$this->merchantService->canAccessMerchantDocument($userId, $documentId)) {
            return response()->json([
                'status' => false,
                'message' => __('profile::messages.access_denied')
            ], 403);
        }

        $fileUrl = $this->merchantService->getMerchantDocumentFileUrl($userId, $documentId);

        return response()->json([
            'status' => true,
            'message' => __('profile::messages.documents_retrieved'),
            'data' => [
                'download_url' => $fileUrl
            ]
        ]);
    }
}