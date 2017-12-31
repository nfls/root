<?php

namespace App\Entity;

use App\Model\Token;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccessTokenRepository")
 */
class AccessToken extends Token implements AccessTokenEntityInterface
{
    public function convertToJWT(CryptKey $privateKey)
    {
        // TODO: Implement convertToJWT() method.
    }

}
