<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\OAuth\AccessToken;
use App\Entity\OAuth\AuthCode;
use App\Entity\OAuth\Client;
use App\Entity\OAuth\RefreshToken;
use App\Entity\OAuth\Scope;
use App\Entity\User\User;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use \DateInterval;

class OAuthService extends Controller{

    const PrivateKey = "/etc/cert/oauth.key";
    const EncryptionKey = "/etc/cert/oauth.pub";
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getServer(){
        $em = $this->em;
        $clientRepo = $em->getRepository(Client::class);
        $accessTokenRepo = $em->getRepository(AccessToken::class);
        $scopeRepo = $em->getRepository(Scope::class);
        $authCodeRepo = $em->getRepository(AuthCode::class);
        $refreshTokenRepo = $em->getRepository(RefreshToken::class);
        $userRepo = $em->getRepository(User::class);


        $refreshTokenExpiry = new DateInterval("P6M");
        $authCodeExpiry = new DateInterval("PT15M");
        $accessTokenExpiry = new DateInterval("P1D");

        $authCodeGrant = new AuthCodeGrant($authCodeRepo,$refreshTokenRepo,$authCodeExpiry);
        $authCodeGrant->setRefreshTokenTTL($refreshTokenExpiry);

        $passwordGrant = new PasswordGrant($userRepo,$refreshTokenRepo);
        $passwordGrant->setRefreshTokenTTL($refreshTokenExpiry);

        $implicitGrant = new ImplicitGrant($accessTokenExpiry);

        $refreshTokenGrant = new RefreshTokenGrant($refreshTokenRepo);
        $refreshTokenGrant->setRefreshTokenTTL($refreshTokenExpiry);

        $server = new AuthorizationServer($clientRepo,$accessTokenRepo,$scopeRepo,self::PrivateKey,self::EncryptionKey);
        $server->enableGrantType($passwordGrant,$accessTokenExpiry);
        $server->enableGrantType($authCodeGrant,$accessTokenExpiry);
        $server->enableGrantType($implicitGrant,$accessTokenExpiry);
        $server->enableGrantType($refreshTokenGrant,$accessTokenExpiry);

        return $server;
    }

    public function getValidator(){
        $em = $this->em;
        $accessTokenRepo = $em->getRepository(AccessToken::class);
        $server = new ResourceServer($accessTokenRepo,self::EncryptionKey);
        return $server;
    }
}
