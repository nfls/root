<?php

namespace App\Repository\OAuth;

use App\Entity\OAuth\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {

        $client = $this->getClientById($clientIdentifier);
        if (@null === $client)
            return null;
        if (!in_array($grantType, $client->getGrantType()))
            return null;
        if ($mustValidateSecret && $clientSecret != $client->getSecret())
            return null;

        return $client;
    }

    public function getClientById($id)
    {
        return $this->findOneBy(["clientId" => $id]);
    }
}
