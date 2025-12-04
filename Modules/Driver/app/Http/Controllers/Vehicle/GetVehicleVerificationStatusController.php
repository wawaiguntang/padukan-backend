<?php

namespace Modules\Driver\Http\Controllers\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Driver\Enums\DocumentTypeEnum;
use Modules\Driver\Services\Vehicle\IVehicleService;
use Modules\Driver\Repositories\Document\IDocumentRepository;
use Modules\Driver\Policies\VehicleManagement\IVehicleManagementPolicy;
use Modules\Driver\Services\Profile\IProfileService;

/**
 * Get Vehicle Verification Status Controller
 *
 * Handles checking specific vehicle verification status
 */
class GetVehicleVerificationStatusController
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
     * Vehicle ownership policy instance
     */
    protected IVehicleManagementPolicy $vehicleManagementPolicy;

    /**
     * Profile service instance
     */
    protected IProfileService $profileService;

    /**
     * Constructor
     */
    public function __construct(
        IVehicleService $vehicleService,
        IDocumentRepository $documentRepository,
        IVehicleManagementPolicy $vehicleManagementPolicy,
        IProfileService $profileService
    ) {
        $this->vehicleService = $vehicleService;
        $this->documentRepository = $documentRepository;
        $this->vehicleManagementPolicy = $vehicleManagementPolicy;
        $this->profileService = $profileService;
    }

    /**
     * Check vehicle verification status
     */
    public function __invoke(Request $request, string $vehicleId): JsonResponse
    {
        $user = $request->authenticated_user;

        $vehicle = $this->vehicleService->getVehicleById($vehicleId);
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

        $profile = $this->profileService->getProfileByUserId($user->id);
        $simDocument = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::SIM)->first();
        $stnkDocument = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::STNK)->first();
        $vehiclePhotos = $this->documentRepository->findByTypeAndProfileId($profile->id, DocumentTypeEnum::VEHICLE_PHOTO);

        return response()->json([
            'status' => true,
            'message' => __('driver::controller.vehicle.verification.status_retrieved'),
            'data' => [
                'vehicle_verified' => $vehicle->is_verified,
                'verification_status' => $vehicle->verification_status,
                'submitted_at' => $vehicle->updated_at,
                'verified_at' => $vehicle->verified_at,
                'documents' => [
                    'sim' => $simDocument ? [
                        'id' => $simDocument->id,
                        'status' => $simDocument->verification_status,
                        'submitted_at' => $simDocument->created_at,
                        'verified_at' => $simDocument->verified_at,
                        'expiry_date' => $simDocument->expiry_date,
                        'temporary_url' => app(\Modules\Driver\Services\FileUpload\IFileUploadService::class)->generateTemporaryUrl($simDocument->file_path),
                    ] : null,
                    'stnk' => $stnkDocument ? [
                        'id' => $stnkDocument->id,
                        'status' => $stnkDocument->verification_status,
                        'submitted_at' => $stnkDocument->created_at,
                        'verified_at' => $stnkDocument->verified_at,
                        'expiry_date' => $stnkDocument->expiry_date,
                        'temporary_url' => app(\Modules\Driver\Services\FileUpload\IFileUploadService::class)->generateTemporaryUrl($stnkDocument->file_path),
                    ] : null,
                    'vehicle_photos' => $vehiclePhotos->map(function ($photo) {
                        return [
                            'id' => $photo->id,
                            'status' => $photo->verification_status,
                            'angle' => $photo->meta['angle'] ?? null,
                            'submitted_at' => $photo->created_at,
                            'verified_at' => $photo->verified_at,
                            'temporary_url' => app(\Modules\Driver\Services\FileUpload\IFileUploadService::class)->generateTemporaryUrl($photo->file_path),
                        ];
                    }),
                ],
                'can_resubmit' => $vehicle->verification_status === \Modules\Driver\Enums\VerificationStatusEnum::REJECTED,
                'can_submit' => $vehicle->verification_status === \Modules\Driver\Enums\VerificationStatusEnum::PENDING,
            ],
        ]);
    }
}
