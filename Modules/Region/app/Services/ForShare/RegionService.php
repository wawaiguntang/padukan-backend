<?php

namespace Modules\Region\Services\ForShare;

use App\Shared\Region\Services\IRegionService;
use Modules\Region\Repositories\Region\IRegionRepository;

class RegionService implements IRegionService
{
    public function __construct(
        protected IRegionRepository $regionRepository
    ) {}

    public function getRegionByCoordinates(float $lat, float $lng): ?array
    {
        $region = $this->regionRepository->findByCoordinates($lat, $lng);

        return $region ? $region->toArray() : null;
    }

    public function isServiceAvailable(string $regionId, string $serviceName): bool
    {
        return $this->regionRepository->isServiceAvailable($regionId, $serviceName);
    }

    public function getActiveRegions(): array
    {
        return $this->regionRepository->getActiveRegions()->toArray();
    }
}
