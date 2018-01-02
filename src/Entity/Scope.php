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
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getIdentifier()
    {
        // TODO: Implement getIdentifier() method.
    }

    function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }


}
