<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GalleryController extends Controller
{
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct()
    {
        $this->response = new ApiResponse();
    }

    /**
     * @Route("/media/gallery/list", methods="GET")
     */
    public function getList(){

    }

    /**
     * @Route("/media/gallery/add", methods="POST")
     */
    public function addGallery(Request $request){
        $title = $request->request->get("title");
        $description = $request->request->get("description");
        $photos = json_decode($request->request->get("photos"));

        $gallery = new Gallery();
        $gallery->setTitle($title);
        $gallery->setDescription($description);
    }
}
