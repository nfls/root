<?php

namespace App\Entity;

use App\Model\Token;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthCodeRepository")
 */
class AuthCode extends Token implements AuthCodeEntityInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $redirectUri;

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function setRedirectUri($uri)
    {
        $this->redirectUri = $uri;
    }

}
