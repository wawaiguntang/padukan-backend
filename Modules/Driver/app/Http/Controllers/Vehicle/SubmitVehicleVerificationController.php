<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Http\Requests\VehicleVerificationRequest;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Services\Document\IDocumentService;
use Modules\Driver\Policies\VehicleManagement\IVehicleManagementPolicy;

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
    protected IVehicleManagementPolicy $vehicleManagementPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IVehicleService $vehicleService,
        IDocumentService $documentService,
        IVehicleManagementPolicy $vehicleManagementPolicy
    ) {
        $this->vehicleService = $vehicleService;
        $this->documentService = $documentService;
        $this->vehicleManagementPolicy = $vehicleManagementPolicy;
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
                'message' => __('driver::controller.vehicle.not_found'),
            ], 404);
        }

        if (!$this->vehicleManagementPolicy->ownsVehicle($user->id, $vehicle->id)) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.vehicle.access_denied'),
            ], 403);
        }


        try {
            if ($vehicle->verification_status === \Modules\Driver\Enums\VerificationStatusEnum::PENDING) {
                $uploadedDocuments = [];

                // Upload SIM document
                $simDocument = $this->documentService->uploadDocument(
                    $user->id,
                    DocumentTypeEnum::SIM,
                    $request->file('sim_file'),
                    [
                        'meta' => $validated['sim_meta'],
                        'expiry_date' => $validated['sim_expiry_date'],
                        'verification_status' => \Modules\Driver\Enums\VerificationStatusEnum::ON_REVIEW,
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
                        'verification_status' => \Modules\Driver\Enums\VerificationStatusEnum::ON_REVIEW,
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
                            'verification_status' => \Modules\Driver\Enums\VerificationStatusEnum::ON_REVIEW,
                        ]
                    );
                    $uploadedDocuments[] = $vehiclePhoto;
                }

                $this->vehicleService->updateVerificationStatus($vehicle->id, false, 'on_review');

                return response()->json([
                    'status' => true,
                    'message' => __('driver::controller.vehicle.verification.submitted_successfully'),
                    'data' => [
                        'vehicle_id' => $vehicle->id,
                        'verification_id' => $vehicle->id,
                        'status' => 'on_review',
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
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::controller.vehicle.verification.cannot_submit'),
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.vehicle.verification.submission_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
