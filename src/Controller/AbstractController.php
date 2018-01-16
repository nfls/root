<?php

namespace App\Controller;

use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends Controller
{
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }
}
