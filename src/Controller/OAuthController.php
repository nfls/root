<?php

namespace App\Controller;

use App\Entity\Client;
use App\Model\ApiResponse;
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

    private $server;
    private $response;

    public function __construct(OAuthService $service)
    {
        $this->server = $service->getServer();
        $this->response = new ApiResponse();
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

    /**
     * @Route("oauth/version", methods="GET")
     */
    public function version(Request $request){
        $repo = $this->getDoctrine()->getManager()->getRepository(Client::class);
        $client = $repo->getClientById($request->query->get("client_id"));
        return $this->response->response($client->getVersion());
    }
}
