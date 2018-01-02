<?php
namespace App\Model;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\TokenInterface;
use App\Entity\Client;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
abstract class Token implements TokenInterface{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     */
    protected $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetimetz")
     */
    protected $expiryTime;

    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    protected $client;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1024)
     */
    protected $scopes;


    public function  __construct()
    {
        $this->scopes = json_encode([]);
    }

    public function getIdentifier()
    {
        return $this->token;
    }

    public function setIdentifier($identifier)
    {
        $this->token = $identifier;
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
        $this->user = $identifier;
    }

    public function getUserIdentifier()
    {
        return $this->user;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
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