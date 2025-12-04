<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\JsonResponse;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Http\Requests\VehicleVerificationRequest;
use Modules\Driver\Repositories\Document\IDocumentRepository;
use Modules\Driver\Services\Document\IDocumentService;
use Modules\Driver\Services\Profile\IProfileService;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Policies\VehicleManagement\IVehicleManagementPolicy;

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
     * Profile service instance
     */
    protected IProfileService $profileService;

    /**
     * Vehicle ownership policy instance
     */
    protected IVehicleManagementPolicy $vehicleManagementPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IVehicleService $vehicleService,
        IDocumentRepository $documentRepository,
        IDocumentService $documentService,
        IProfileService $profileService,
        IVehicleManagementPolicy $vehicleManagementPolicy
    ) {
        $this->vehicleService = $vehicleService;
        $this->documentRepository = $documentRepository;
        $this->documentService = $documentService;
        $this->profileService = $profileService;
        $this->vehicleManagementPolicy = $vehicleManagementPolicy;
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
            if ($vehicle->verification_status === \Modules\Driver\Enums\VerificationStatusEnum::REJECTED) {
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

                $this->vehicleService->updateVerificationStatus($vehicle->id, false, 'on_review');

                return response()->json([
                    'status' => true,
                    'message' => __('driver::controller.vehicle.verification.resubmitted_successfully'),
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
                        'resubmitted_at' => now(),
                    ],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('driver::controller.vehicle.verification.resubmit_not_allowed'),
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('driver::controller.vehicle.verification.resubmission_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
