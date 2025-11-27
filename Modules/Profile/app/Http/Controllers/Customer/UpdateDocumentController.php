<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerDocumentService;
use Modules\Profile\Http\Requests\UpdateDocumentRequest;
use Modules\Profile\Http\Resources\DocumentResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\DocumentNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Update Document Controller
 *
 * Handles updating customer documents
 */
class UpdateDocumentController extends Controller
{
    private ICustomerDocumentService $documentService;

    public function __construct(ICustomerDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Update customer document
     */
    public function updateDocument(UpdateDocumentRequest $request, string $documentId): JsonResponse
    {
        try {
            $userId = $request->authenticated_user_id;
            $validatedData = $request->validated();

            $data = $this->documentService->updateDocument($userId, $documentId, $validatedData);

            return response()->json(
                new DocumentResponseResource($data, 'profile.document.updated_successfully')
            );
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