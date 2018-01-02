<?php

namespace App\Repository;

use App\Entity\AccessToken;
use App\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RefreshTokenRepository extends ServiceEntityRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function getNewRefreshToken()
    {
        $token = new RefreshToken();
        return $token;
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $em = $this->getEntityManager();
        $em->persist($refreshTokenEntity);
        $em->flush();
    }

    public function revokeRefreshToken($tokenId)
    {
        $em = $this->getEntityManager();
        $em->remove($this->findOneBy(["token"=>$tokenId]));
        $em->flush();
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        $token = $this->findOneBy(["token"=>$tokenId]);
        if(@null === $token)
            return true;
        else
            return false;
    }


}
