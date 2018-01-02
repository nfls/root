<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScopeRepository")
 */
class Scope implements ScopeEntityInterface
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $role;

    public function getIdentifier()
    {
       return $this->token;
    }

    function jsonSerialize()
    {
        return $this->getIdentifier();
    }


}
