<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerDocumentService;
use Modules\Profile\Http\Resources\DocumentsResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Get Documents Controller
 *
 * Handles getting customer documents
 */
class GetDocumentsController extends Controller
{
    private ICustomerDocumentService $documentService;

    public function __construct(ICustomerDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Get customer documents
     */
    public function getDocuments(): JsonResponse
    {
        try {
            $userId = request()->authenticated_user_id;

            $data = $this->documentService->getDocuments($userId);

            return response()->json(
                new DocumentsResponseResource($data, 'profile.documents.retrieved_successfully')
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