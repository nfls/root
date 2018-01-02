<?php

namespace App\Controller;

use App\Service\OAuthService;
use League\OAuth2\Server\Exception\OAuthServerException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuthController extends Controller
{

    public $server;

    public function __construct(OAuthService $service)
    {
        $this->server = $service->getServer();
    }


    /**
     * @Route("/oauth/authorize")
     */
    public function authorize(Request $request){
        return JsonResponse::create(null,404);
    }

    /**
     * @Route("/oauth/accessToken")
     */
    public function accessToken(Request $request){
        $factory = new DiactorosFactory();
        $httpFoundationFactory = new HttpFoundationFactory();
        $psrResponse = $factory->createResponse(new Response());
        $psrRequest = $factory->createRequest($request);
        try{
            return $httpFoundationFactory->createResponse($this->server->respondToAccessTokenRequest($psrRequest,$psrResponse));

        }catch (OAuthServerException $e){

            return $httpFoundationFactory->createResponse($e->generateHttpResponse($psrResponse));

        }
    }
}
