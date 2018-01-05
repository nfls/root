<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GalleryController extends Controller
{
    /**
     * @var ApiResponse
     */
    private $response;

    CONST THUMB_FOLDER = "storage/photos/thumb";
    CONST HD_FOLDER = "storage/photos/hd";
    const ORIGIN_FOLDER = "storage/photos/origin";

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
     * @Route("/media/admin/upload", methods="GET")
     */
    public function uploadPage(){
        return $this->render("upload.photos.html");
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

    /**
     * @Route("media/gallery/upload", methods="POST")
     */
    publiC function addPhoto(Request $request){
        //if($this->getUser()->)
        //TODO ROLE CHECK
        $allowOrigin = $request->request->get("allowOrigin");
        $originalPhoto = $request->files->get("photo");
        $path = $originalPhoto->getRealPath();

        $thumb = new \Imagick($path);
        $hd = new \Imagick($path);

        $thumb->thumbnailImage(300,300,true);
        $hd->resizeImage(2048,2048,\Imagick::FILTER_LANCZOS,1,true);

        $thumb->setImageFormat("png");
        $hd->setImageFormat("png");


        $thumb->writeImage($path.".thumb.png");
        $hd->writeImage($path.".hd.png");

        exec("cwebp -q 80 ".$path.".thumb.png -o ".$path.".thumb.webp ");
        exec("cwebp -q 90 ".$path.".hd.png -o ".$path.".hd.webp ");
        exec("cwebp -q 100 ".$path." -o ".$path.".webp ");

        $thumbPhoto = new File($path.".thumb.webp");
        $hdPhoto = new File($path.".hd.webp");

        unlink($path.".thumb.png");
        unlink($path.".hd.png");

        $thumbName = bin2hex(random_bytes(64)).".webp";
        $hdName = bin2hex(random_bytes(64)).".webp";
        $originName = bin2hex(random_bytes(64)).".webp";

        $thumbPhoto->move(self::THUMB_FOLDER, $thumbName);
        $hdPhoto->move(self::HD_FOLDER, $hdName);

        $photo = new Photo();
        $photo->setHd($hdPhoto->getPath());
        $photo->setThumb($thumbPhoto->getPath());

        if($allowOrigin){
            $originPhoto = new File($path.".webp");
            $originPhoto->move(self::ORIGIN_FOLDER, $originName);
            $photo->setOrigin($originPhoto->getPath());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($photo);
        $em->flush();

        return $this->response->response(array(
            "hd" => $hdName,
            "thumb" => $thumbName
        ),200);
    }
}
