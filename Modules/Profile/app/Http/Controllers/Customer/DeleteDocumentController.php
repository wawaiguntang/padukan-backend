<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerDocumentService;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\DocumentNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Delete Document Controller
 *
 * Handles deleting customer documents
 */
class DeleteDocumentController extends Controller
{
    private ICustomerDocumentService $documentService;

    public function __construct(ICustomerDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Delete customer document
     */
    public function deleteDocument(string $documentId): JsonResponse
    {
        try {
            $userId = request()->authenticated_user_id;

            $this->documentService->deleteDocument($userId, $documentId);

            return response()->json([
                'status' => true,
                'message' => __('profile::messages.document_deleted_successfully'),
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