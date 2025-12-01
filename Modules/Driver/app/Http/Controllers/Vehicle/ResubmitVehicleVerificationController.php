<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\VehicleVerificationRequest;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Repositories\Document\IDocumentRepository;
use Modules\Driver\Services\Document\IDocumentService;
use Modules\Driver\Policies\VehicleOwnership\IVehicleOwnershipPolicy;

/**
 * Resubmit Vehicle Verification Controller
 *
 * Handles resubmitting vehicle verification (only if rejected)
 */
class ResubmitVehicleVerificationController
{
    /**
     * Vehicle service instance
     */
    protected IVehicleService $vehicleService;

    /**
     * Document repository instance
     */
    protected IDocumentRepository $documentRepository;

    /**
     * Document service instance
     */
    protected IDocumentService $documentService;

    /**
     * Vehicle ownership policy instance
     */
    protected IVehicleOwnershipPolicy $vehicleOwnershipPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IVehicleService $vehicleService,
        IDocumentRepository $documentRepository,
        IDocumentService $documentService,
        IVehicleOwnershipPolicy $vehicleOwnershipPolicy
    ) {
        $this->vehicleService = $vehicleService;
        $this->documentRepository = $documentRepository;
        $this->documentService = $documentService;
        $this->vehicleOwnershipPolicy = $vehicleOwnershipPolicy;
    }

    /**
     * Resubmit vehicle verification (only if rejected)
     */
    public function __invoke(VehicleVerificationRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        $vehicle = $this->vehicleService->getVehicleById($validated['vehicle_id']);
        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.not_found'),
            ], 404);
        }

        // Check if user can resubmit vehicle verification
        if (!$this->vehicleOwnershipPolicy->canResubmitVehicleVerification($user->id, $vehicle->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.verification.resubmit_not_allowed'),
            ], 400);
        }

        try {
            // Get profile for document operations
            $profile = app(\Modules\Driver\Repositories\Profile\IProfileRepository::class)->findByUserId($user->id);

            // Delete existing verification documents
            $existingSim = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::SIM)->first();
            if ($existingSim) {
                $this->documentService->deleteDocument($existingSim->id);
            }

            $existingStnk = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::STNK)->first();
            if ($existingStnk) {
                $this->documentService->deleteDocument($existingStnk->id);
            }

            $existingPhotos = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::VEHICLE_PHOTO);
            foreach ($existingPhotos as $photo) {
                $this->documentService->deleteDocument($photo->id);
            }

            // Upload new documents (same logic as submitVerification)
            $uploadedDocuments = [];

            $simDocument = $this->documentService->uploadDocument(
                $user->id,
                DocumentTypeEnum::SIM,
                $request->file('sim_file'),
                [
                    'meta' => $validated['sim_meta'],
                    'expiry_date' => $validated['sim_expiry_date'],
                ]
            );
            $uploadedDocuments[] = $simDocument;

            $stnkDocument = $this->documentService->uploadDocument(
                $user->id,
                DocumentTypeEnum::STNK,
                $request->file('stnk_file'),
                [
                    'expiry_date' => $validated['stnk_expiry_date'],
                ]
            );
            $uploadedDocuments[] = $stnkDocument;

            foreach ($validated['vehicle_photos'] as $index => $photoFile) {
                $vehiclePhoto = $this->documentService->uploadDocument(
                    $user->id,
                    DocumentTypeEnum::VEHICLE_PHOTO,
                    $photoFile,
                    [
                        'meta' => ['angle' => $index + 1],
                    ]
                );
                $uploadedDocuments[] = $vehiclePhoto;
            }

            // Reset verification status to pending
            $this->vehicleService->updateVerificationStatus($vehicle->id, false, 'pending');

            return response()->json([
                'status' => true,
                'message' => __('driver::vehicle.verification.resubmitted_successfully'),
                'data' => [
                    'vehicle_id' => $vehicle->id,
                    'verification_id' => $vehicle->id,
                    'status' => 'pending',
                    'documents_uploaded' => count($uploadedDocuments),
                    'documents' => array_map(function ($document) {
                        return [
                            'id' => $document->id,
                            'type' => $document->type,
                            'file_name' => $document->file_name,
                            'uploaded_at' => $document->created_at,
                            'temporary_url' => app(\Modules\Driver\Services\FileUpload\IFileUploadService::class)->generateTemporaryUrl($document->file_path),
                        ];
                    }, $uploadedDocuments),
                    'resubmitted_at' => now(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.verification.resubmission_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
