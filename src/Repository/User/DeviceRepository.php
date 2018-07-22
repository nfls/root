<?php

namespace App\Repository\User;

use App\Entity\User\Device;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Device::class);
    }

    public function findOneByUserAndToken(User $user, string $token) {
        return $this->findOneBy(["user" => $user, "token" => $token]);
    }

    public function findOneByCallbackToken(string $token) {
        return $this->findOneBy(["callbackToken" => $token]);
    }
}
