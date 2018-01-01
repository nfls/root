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
     * @var string
     *
     * @ORM\Column(type="string",length=128)
     */
    protected $accessToken;

    /**
     * @var AccessTokenRepository
     */
    private $accessRepo;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
        $this->accessRepo = $entityManager->getRepository(AccessToken::class);
    }

    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->token = $accessToken->getIdentifier();
    }

    public function getAccessToken()
    {
        return $this->accessRepo->getTokenById($this->accessToken);
    }


}
