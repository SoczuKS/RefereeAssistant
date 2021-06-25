<?php

namespace App\Service;

use App\Entity\City;
use App\Repository\CityRepository;

class CityService
{
    private $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function saveCity(City $city): void
    {
        $this->cityRepository->save($city);
    }
}