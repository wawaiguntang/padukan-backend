<?php

namespace Modules\Driver\Services\Vehicle;

use Illuminate\Database\Eloquent\Collection;
use Modules\Driver\Models\Vehicle;
use Modules\Driver\Repositories\Vehicle\IVehicleRepository;
use Modules\Driver\Repositories\Profile\IProfileRepository;
use Modules\Driver\Exceptions\ProfileNotFoundException;

/**
 * Vehicle Service Implementation
 *
 * This class handles vehicle business logic operations
 * for the driver module.
 */
class VehicleService implements IVehicleService
{
    /**
     * The vehicle repository instance
     *
     * @var IVehicleRepository
     */
    protected IVehicleRepository $vehicleRepository;

    /**
     * The profile repository instance
     *
     * @var IProfileRepository
     */
    protected IProfileRepository $profileRepository;


    /**
     * Constructor
     *
     * @param IVehicleRepository $vehicleRepository The vehicle repository instance
     * @param IProfileRepository $profileRepository The profile repository instance
     */
    public function __construct(
        IVehicleRepository $vehicleRepository,
        IProfileRepository $profileRepository
    ) {
        $this->vehicleRepository = $vehicleRepository;
        $this->profileRepository = $profileRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function createVehicle(string $userId, array $data): Vehicle
    {
        // Find user profile
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException();
        }

        // Prepare vehicle data with profile_id
        $vehicleData = array_merge($data, ['driver_profile_id' => $profile->id]);

        return $this->vehicleRepository->create($vehicleData);
    }

    /**
     * {@inheritDoc}
     */
    public function getVehiclesByUserId(string $userId): Collection
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException();
        }

        return $this->vehicleRepository->findByProfileId($profile->id);
    }

    /**
     * {@inheritDoc}
     */
    public function getVehicleById(string $vehicleId): ?Vehicle
    {
        return $this->vehicleRepository->findById($vehicleId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateVehicle(string $vehicleId, array $data): bool
    {
        // Check if user can modify this vehicle (we'll need userId from somewhere)
        // For now, we'll assume the caller has already checked permissions
        // In a real implementation, this method should receive userId as parameter

        return $this->vehicleRepository->update($vehicleId, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function updateVerificationStatus(string $vehicleId, bool $isVerified, ?string $verificationStatus = null): bool
    {
        return $this->vehicleRepository->updateVerificationStatus($vehicleId, $isVerified, $verificationStatus);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteVehicle(string $vehicleId): bool
    {
        return $this->vehicleRepository->delete($vehicleId);
    }
}
