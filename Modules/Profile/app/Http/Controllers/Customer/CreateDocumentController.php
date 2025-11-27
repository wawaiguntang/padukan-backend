<?php

namespace Modules\Profile\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Profile\Services\Customer\ICustomerDocumentService;
use Modules\Profile\Http\Requests\CreateDocumentRequest;
use Modules\Profile\Http\Resources\DocumentResponseResource;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

/**
 * Create Document Controller
 *
 * Handles creating customer documents
 */
class CreateDocumentController extends Controller
{
    private ICustomerDocumentService $documentService;

    public function __construct(ICustomerDocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Create customer document
     */
    public function createDocument(CreateDocumentRequest $request): JsonResponse
    {
        try {
            $userId = $request->authenticated_user_id;
            $validatedData = $request->validated();

            $data = $this->documentService->createDocument($userId, $validatedData);

            return response()->json(
                new DocumentResponseResource($data, 'profile.document.created_successfully'),
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