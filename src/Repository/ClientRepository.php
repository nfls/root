<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $client = $this->getClientById($clientIdentifier);
        if(@null === $client)
            return null;
        if($grantType != $client->getGrantType())
            return null;
        if($mustValidateSecret && $clientSecret != $client->getSecret())
            return null;

        return $client;

        // TODO: Implement getClientEntity() method.
    }

    public function getClientById($id){
        return $this->findOneBy(["clientId"=>$id]);
    }

}
