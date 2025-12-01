<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\DocumentUploadRequest;
use Modules\Customer\Http\Resources\DocumentResource;
use Modules\Customer\Repositories\Document\IDocumentRepository;
use Modules\Customer\Repositories\Profile\IProfileRepository;
use Modules\Customer\Services\Profile\IProfileService;
use Modules\Customer\Services\FileUpload\IFileUploadService;
use Modules\Customer\Services\Document\IDocumentService;

/**
 * Document Controller
 *
 * Handles customer document operations with auto-profile creation
 */
class DocumentController
{
    /**
     * Document repository instance
     */
    protected IDocumentRepository $documentRepository;

    /**
     * Profile repository instance
     */
    protected IProfileRepository $profileRepository;

    /**
     * Profile service instance
     */
    protected IProfileService $profileService;

    /**
     * File upload service instance
     */
    protected IFileUploadService $fileUploadService;

    /**
     * Document service instance
     */
    protected IDocumentService $documentService;

    /**
     * Constructor
     */
    public function __construct(
        IDocumentRepository $documentRepository,
        IProfileRepository $profileRepository,
        IProfileService $profileService,
        IFileUploadService $fileUploadService,
        IDocumentService $documentService
    ) {
        $this->documentRepository = $documentRepository;
        $this->profileRepository = $profileRepository;
        $this->profileService = $profileService;
        $this->fileUploadService = $fileUploadService;
        $this->documentService = $documentService;
    }

    /**
     * Get customer documents
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get or create profile
        $profile = $this->profileRepository->findByUserId($user->id);
        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        $documents = $this->documentRepository->findByProfileId($profile->id);

        return response()->json([
            'status' => true,
            'message' => __('customer::document.retrieved_successfully'),
            'data' => DocumentResource::collection($documents),
        ]);
    }

    /**
     * Upload new document
     */
    public function store(DocumentUploadRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Get or create profile
        $profile = $this->profileRepository->findByUserId($user->id);
        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        try {
            // Upload file
            $uploadResult = $this->fileUploadService->uploadDocument(
                $request->file('file'),
                $validated['type'],
                $profile->id
            );

            // Create document record
            $documentData = [
                'profile_id' => $profile->id,
                'type' => $validated['type'],
                'file_path' => $uploadResult['path'],
                'file_name' => $uploadResult['original_name'],
                'mime_type' => $uploadResult['mime_type'],
                'file_size' => $uploadResult['size'],
                'expiry_date' => $validated['expiry_date'] ?? null,
            ];

            $document = $this->documentRepository->create($documentData);

            return response()->json([
                'status' => true,
                'message' => __('customer::document.uploaded_successfully'),
                'data' => new DocumentResource($document),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('customer::document.upload_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specific document
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $document = $this->documentRepository->findById($id);

        if (!$document) {
            return response()->json([
                'status' => false,
                'message' => __('customer::document.not_found'),
            ], 404);
        }

        // Check ownership
        $profile = $this->profileRepository->findByUserId($user->id);
        if (!$profile || $document->profile_id !== $profile->id) {
            return response()->json([
                'status' => false,
                'message' => __('customer::document.access_denied'),
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => __('customer::document.retrieved_successfully'),
            'data' => new DocumentResource($document),
        ]);
    }

    /**
     * Delete document
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $document = $this->documentRepository->findById($id);

        if (!$document) {
            return response()->json([
                'status' => false,
                'message' => __('customer::document.not_found'),
            ], 404);
        }

        // Check ownership
        $profile = $this->profileRepository->findByUserId($user->id);
        if (!$profile || $document->profile_id !== $profile->id) {
            return response()->json([
                'status' => false,
                'message' => __('customer::document.access_denied'),
            ], 403);
        }

        try {
            // Delete file
            $this->fileUploadService->deleteDocument($document->file_path);

            // Delete document record
            $deleted = $this->documentRepository->delete($id);

            if (!$deleted) {
                return response()->json([
                    'status' => false,
                    'message' => __('customer::document.delete_failed'),
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => __('customer::document.deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('customer::document.delete_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
