<?php

namespace App\Repository\OAuth;

use App\Entity\OAuth\Scope;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ScopeRepository extends ServiceEntityRepository implements ScopeRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Scope::class);
    }

    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->findOneBy(["token"=>$identifier]);
    }

    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    )
    {
        return $scopes;
    }


}
