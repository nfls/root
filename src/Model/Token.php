<?php
namespace App\Model;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\TokenInterface;

abstract class Token implements TokenInterface {

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz")
     */
    private $expiryTime;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    private $scopes;

    public function  __construct()
    {
        $this->scopes = json_decode([]);
    }

    public function getIdentifier()
    {
        return $this->clientId;
    }

    public function setIdentifier($identifier)
    {
        $this->clientId = $identifier;
    }

    public function getExpiryDateTime()
    {
        return $this->expiryTime;
    }

    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->expiryTime = $dateTime;
    }

    public function setUserIdentifier($identifier)
    {
        $this->uid = $identifier;
    }

    public function getUserIdentifier()
    {
        return $this->uid;
    }

    public function getClient()
    {
        // TODO: Implement getClient() method.
    }

    public function setClient(ClientEntityInterface $client)
    {
        // TODO: Implement setClient() method.
    }

    public function addScope(ScopeEntityInterface $scope)
    {
        $scopes = json_decode($this->scopes, true);
        if(!in_array($scope->getIdentifier(),$scopes)){
            array_push($scopes,$scope->getIdentifier());
        }
        return json_encode($scopes);
    }

    public function getScopes()
    {
        //TODO
    }


}
