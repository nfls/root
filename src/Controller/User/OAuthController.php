<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Entity\OAuth\Client;
use App\Model\Permission;
use App\Service\AliyunOSS;
use App\Service\OAuthService;
use League\OAuth2\Server\Exception\OAuthServerException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuthController extends AbstractController
{
    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    private $server;

    public function __construct(OAuthService $service)
    {
        $this->server = $service->getServer();
        parent::__construct();
    }


    /**
     * @Route("/oauth/authorize")
     */
    public function authorize(Request $request)
    {
        $user = $this->getUser();
        $factory = new DiactorosFactory();
        $httpFoundationFactory = new HttpFoundationFactory();
        $psrRequest = $factory->createRequest($request);
        $psrResponse = $factory->createResponse(new Response());
        if (null === $user) {
            return new RedirectResponse("/#/user/login?redirect=" . urlencode($request->getUri()));
        }
        if (is_null($user->getEmail())) {
            return new RedirectResponse("/#/user/security?reason=email");
        }
        try {
            $authRequest = $this->server->validateAuthorizationRequest($psrRequest);
            $authRequest->setUser($user);
            $authRequest->setAuthorizationApproved(true);
            $response = $httpFoundationFactory->createResponse($this->server->completeAuthorizationRequest($authRequest, $psrResponse));
            $response_array = json_decode($response->getContent(),true);
            $response_array["id_token"] = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJPbmxpbmUgSldUIEJ1aWxkZXIiLCJpYXQiOjE1MTk4NjQxNTUsImV4cCI6MTU1MTQwMDE1NSwiYXVkIjoid3d3LmV4YW1wbGUuY29tIiwic3ViIjoianJvY2tldEBleGFtcGxlLmNvbSJ9.M_2dughXyVmbuem2_EQFh0Cjw7Kutcqt6rSi1I7jR1I';
            $response->setContent(json_encode($response_array));
            return $response;
        } catch (OAuthServerException $e) {
            return $this->response()->response($e->getMessage(), 404);
            //return $httpFoundationFactory->createResponse($e->generateHttpResponse($psrResponse));
        }
    }

    /**
     * @Route("/oauth/accessToken")
     */
    public function accessToken(Request $request)
    {
        $factory = new DiactorosFactory();
        $httpFoundationFactory = new HttpFoundationFactory();
        $psrResponse = $factory->createResponse(new Response());
        $psrRequest = $factory->createRequest($request);
        try {
            return $httpFoundationFactory->createResponse($this->server->respondToAccessTokenRequest($psrRequest, $psrResponse));
        } catch (OAuthServerException $e) {
            return $httpFoundationFactory->createResponse($e->generateHttpResponse($psrResponse));

        }
    }

    /**
     * @Route("oauth/version", methods="GET")
     */
    public function version(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Client::class);
        $client = $repo->getClientById($request->query->get("client_id"));
        return $this->response()->response($client->getVersion());
    }

    /**
     * @Route("oauth/uploadSts", methods="GET")
     */
    public function uploadSts(Request $request, AliyunOSS $oss)
    {
        return $this->response()->response($oss->getUploadToken($this->getUser()->getUsername()), 200);
    }

    /**
     * @Route("oauth/uploadSignature", methods="GET")
     */
    public function uploadSignature(Request $request, AliyunOSS $oss)
    {

        return $this->response()->response($oss->getSignature());
    }

    /**
     * @Route("admin/basic/upload", methods="GET")
     */
    public function renderUploadPage()
    {
        $this->denyAccessUnlessGranted(Permission::IS_ADMIN);
        return $this->render("admin/basic/upload.html.twig");
    }

    /**
     * @Route("admin/basic/oauth", methods="GET")
     */
    public function renderOAuthPage()
    {
        $this->denyAccessUnlessGranted(Permission::IS_ADMIN);
        return $this->render("admin/basic/oauth.html.twig");
    }


    /**
     * @Route("admin/basic/oauth/edit")
     */
    public function getOAuthList(Request $request)
    {
        $this->denyAccessUnlessGranted(Permission::IS_ADMIN);
        $repo = $this->getDoctrine()->getManager()->getRepository(Client::class);
        if ($request->request->has("client_id")) {
            $client = $repo->getClientById($request->request->get("client_id"));
            $client->setVersion($request->request->get("version"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();
            //return $this->response()->responseEntity($client);
        }
        return $this->response()->responseEntity($repo->findAll());
    }

}
