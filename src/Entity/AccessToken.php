<?php

namespace App\Entity;

use App\Model\Token;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccessTokenRepository")
 */
class AccessToken extends Token implements AccessTokenEntityInterface
{
    use AccessTokenTrait;

}
