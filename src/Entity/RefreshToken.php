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
class RefreshToken implements RefreshTokenEntityInterface
{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     */
    protected $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz")
     */
    protected $expiryTime;

    /**
     * @var AccessTokenEntityInterface
     *
     * @ORM\OneToOne(targetEntity="App\Entity\AccessToken", mappedBy="token")
     * @ORM\JoinColumn(name="accessToken", referencedColumnName="token")
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


    public function getIdentifier()
    {
        return $this->token;
    }

    public function setIdentifier($identifier)
    {
        $this->token = $identifier;
    }

    public function getExpiryDateTime()
    {
        return $this->expiryTime;
    }

    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->expiryTime = $dateTime;
    }
}
