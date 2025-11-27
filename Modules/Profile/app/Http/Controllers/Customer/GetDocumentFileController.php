<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerDocumentService;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\DocumentNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Get Document File Controller
 *
 * Handles getting document file URLs for download
 */
class GetDocumentFileController extends Controller
{
    private ICustomerDocumentService $documentService;

    public function __construct(ICustomerDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Get document file URL for download
     */
    public function getDocumentFile(string $documentId): JsonResponse
    {
        try {
            $userId = request()->authenticated_user_id;

            $fileUrl = $this->documentService->getDocumentFileUrl($userId, $documentId);

            return response()->json([
                'status' => true,
                'message' => __('profile::document_file_url_retrieved'),
                'data' => [
                    'file_url' => $fileUrl,
                ],
            ]);
        } catch (ProfileNotFoundException | DocumentNotFoundException | UnauthorizedAccessException $e) {
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