<?php

namespace App\Repository\OAuth;

use App\Entity\OAuth\AccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AccessTokenRepository extends ServiceEntityRepository implements AccessTokenRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    public function getTokenById($tokenId){
        return $this->findOneBy(["token"=>$tokenId]);
    }


    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessToken();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope){
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);
        return $accessToken;

    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->getEntityManager()->persist($accessTokenEntity);
    }

    public function revokeAccessToken($tokenId)
    {
        $token = $this->findOneBy(["token" => $tokenId]);
        $this->getEntityManager()->remove($token);
        $this->getEntityManager()->flush();
    }

    public function isAccessTokenRevoked($tokenId)
    {
        $token = $this->findOneBy(["token" => $tokenId]);
        if(@null === $token)
            return true;
        else
            return false;
    }


}
