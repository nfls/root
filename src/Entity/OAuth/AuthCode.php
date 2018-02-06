<?php

namespace App\Entity\OAuth;

use App\Model\Token;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OAuth\AuthCodeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AuthCode extends Token implements AuthCodeEntityInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $redirectUri;

    public function __construct()
    {
        $this->scopes = new ArrayCollection();
        parent::__construct();
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function setRedirectUri($uri)
    {
        $this->redirectUri = $uri;
    }
}
