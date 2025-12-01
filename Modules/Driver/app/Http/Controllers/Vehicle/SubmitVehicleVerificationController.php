<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\VehicleVerificationRequest;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Services\Document\IDocumentService;
use Modules\Driver\Policies\VehicleOwnership\IVehicleOwnershipPolicy;

/**
 * Submit Vehicle Verification Controller
 *
 * Handles submitting vehicle verification with required documents
 */
class SubmitVehicleVerificationController
{
    /**
     * Vehicle service instance
     */
    protected IVehicleService $vehicleService;

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
        IDocumentService $documentService,
        IVehicleOwnershipPolicy $vehicleOwnershipPolicy
    ) {
        $this->vehicleService = $vehicleService;
        $this->documentService = $documentService;
        $this->vehicleOwnershipPolicy = $vehicleOwnershipPolicy;
    }

    /**
     * Submit vehicle verification with required documents
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

        // Check if user can submit vehicle verification
        if (!$this->vehicleOwnershipPolicy->canSubmitVehicleVerification($user->id, $vehicle->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.verification.cannot_submit'),
            ], 400);
        }

        try {
            $uploadedDocuments = [];

            // Upload SIM document
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

            // Upload STNK document
            $stnkDocument = $this->documentService->uploadDocument(
                $user->id,
                DocumentTypeEnum::STNK,
                $request->file('stnk_file'),
                [
                    'expiry_date' => $validated['stnk_expiry_date'],
                ]
            );
            $uploadedDocuments[] = $stnkDocument;

            // Upload vehicle photos (4 angles)
            foreach ($validated['vehicle_photos'] as $index => $photoFile) {
                $vehiclePhoto = $this->documentService->uploadDocument(
                    $user->id,
                    DocumentTypeEnum::VEHICLE_PHOTO,
                    $photoFile,
                    [
                        'meta' => ['angle' => $index + 1], // 1=front, 2=back, 3=left, 4=right
                    ]
                );
                $uploadedDocuments[] = $vehiclePhoto;
            }

            // Update vehicle verification status to pending
            $this->vehicleService->updateVerificationStatus($vehicle->id, false, 'pending');

            return response()->json([
                'status' => true,
                'message' => __('driver::vehicle.verification.submitted_successfully'),
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
                    'submitted_at' => now(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::vehicle.verification.submission_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
