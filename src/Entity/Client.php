<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client implements ClientEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=1024)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=128)
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=128)
     */
    private $clientSecret;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=128)
     */
    private $grantType;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=1024)
     */
    private $redirectUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string",length=1024)
     */
    private $version;

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUrl;
    }

    /**
     * @return string
     */
    public function getSecret(){
        return $this->clientSecret;
    }


    /**
     * @return string
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }



}
