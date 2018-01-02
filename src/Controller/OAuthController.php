<?php

namespace App\Controller;

use App\Entity\AccessToken;
use App\Entity\AuthCode;
use App\Entity\Client;
use App\Entity\RefreshToken;
use App\Entity\Scope;
use App\Entity\User;
use App\Repository\ClientRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use Mrgoon\AliyunSmsSdk\DefaultAcsClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \DateInterval;

class OAuthController extends Controller
{

    public $server;

    const PrivateKey = "/etc/cert/oauth.key";
    const EncryptionKey = "/etc/cert/oauth.pub";

    public function init()
    {
        $em = $this->getDoctrine()->getManager();
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

        $this->server = new AuthorizationServer($clientRepo,$accessTokenRepo,$scopeRepo,self::PrivateKey,self::EncryptionKey);
        $this->server->enableGrantType($passwordGrant,$accessTokenExpiry);
        $this->server->enableGrantType($authCodeGrant,$accessTokenExpiry);
        $this->server->enableGrantType($implicitGrant,$accessTokenExpiry);
    }

    /**
     * @Route("/oauth/authorize",name"OAuth2.0 Authorize")
     */
    public function authorize(Request $request){
        return JsonResponse::create(null,404);
    }

    /**
     * @Route("/oauth/accessToken",name="OAuth2.0 AccessToken")
     */
    public function accessToken(Request $request){
        $this->init();
        $factory = new DiactorosFactory();
        $httpFoundationFactory = new HttpFoundationFactory();
        $psrResponse = $factory->createResponse(new Response());
        $psrRequest = $factory->createRequest($request);
        try{
            return $httpFoundationFactory->createResponse($this->server->respondToAccessTokenRequest($psrRequest,$psrResponse));

        }catch (OAuthServerException $e){

            return $httpFoundationFactory->createResponse($e->generateHttpResponse($psrResponse));

        }catch (\Exception $e){
            return JsonResponse::create(null,Response::HTTP_UNAUTHORIZED);
        }
    }
}
