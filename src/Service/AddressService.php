<?php

namespace App\Service;

use App\Entity\Address;
use App\Repository\AddressRepository;

class AddressService
{
    private $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function saveAddress(Address $address)
    {
        $this->addressRepository->save($address);
    }
}