<?php

namespace App\Entity;

use App\Model\Token;
use App\Repository\AccessTokenRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RefreshTokenRepository")
 */
class RefreshToken extends Token implements RefreshTokenEntityInterface
{

    /**
     * @var AccessTokenEntityInterface
     *
     * @ORM\OneToOne(targetEntity="App\Entity\AccessToken", mappedBy="token")
     */
    protected $accessToken;


    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->token = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }


}
