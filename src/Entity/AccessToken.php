<?php

namespace App\Entity;

use App\Model\Token;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccessTokenRepository")
 */
class AccessToken extends Token implements AccessTokenEntityInterface,UserInterface
{
    use AccessTokenTrait;

    public function getRoles()
    {
        $scopes = $this->getScopes();
        // TODO
    }

    public function getPassword()
    {
        return $this->token;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->user->getUsername();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


}
