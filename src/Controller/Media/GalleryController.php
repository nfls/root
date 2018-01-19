<?php

namespace App\Controller\Media;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Model\ApiResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @Route("/admin/media/upload", methods="GET")
     */
    public function uploadPage(){
        return $this->render("admin/media/upload.html.twig");
    }

    /**
     * @Route("/admin/media/photo", methods="GET")
     */
    public function managePhotos()
    {
        return $this->render("admin/media/photo.html.twig");
    }

    /**
     * @Route("admin/media/gallery", methods="GET")
     */
    public function manageGallery(){
        return $this->render("admin/media/gallery.html.twig");
    }

    /**
     * @Route("/admin/media/gallery/edit", methods="POST")
     */
    public function editGallery(Request $request){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Gallery::class);
        if($request->request->has("id")){
            $gallery = $repo->getGallery($request->request->get("id")) ?? new Gallery();
            if($request->request->get("delete") == "true"){
                foreach ($gallery->GetPhotos() as $photo){
                    $photo->setGallery(null);
                    $em->remove($photo);
                }
                $em->remove($gallery);
            }else{
                $title = $request->request->get("title") ?? "";
                $description = $request->request->get("description") ?? "";
                $gallery->setTitle($title);
                $gallery->setDescription($description);
                $gallery->setIsPublic($request->request->get("public") == "true");
                $gallery->setIsVisible($request->request->get("visible") == "true");
                $em->persist($gallery);
            }
            $em->flush();
        }
        $list = $repo->getAllList();
        return $this->response->responseEntity($list,200);
    }

    /**
     * @Route("/admin/media/photo/edit", methods="POST")
     */
    public function editPhotos(Request $request){
        $em = $this->getDoctrine()->getManager();
        $photoRepo = $em->getRepository(Photo::class);
        $galleryRepo = $em->getRepository(Gallery::class);
        $page = $request->request->get("page") ?? 1;
        $rows = $request->request->get("rows") ?? 10;
        $filter = $request->query->get("filter");
        $content = json_decode($request->getContent(),true);
        if(@!is_null($content)){
            foreach($content["id"] as $id){
                $photo = $photoRepo->getPhoto($id);
                if($content["delete"] == "true"){
                    $em->remove($photo);
                }else{
                    $gallery = $galleryRepo->getGallery($content["gallery"]);
                    $photo->setGallery($gallery);
                    $photo->setIsPublic($content["public"]);
                    $photo->setIsVisible($content["visible"]);
                    $em->persist($photo);
                }

            }
            $em->flush();
        }
        if($filter == "all"){
            $showAll = true;
            $name = null;
        }else{
            $showAll = false;
            if($filter == "null"){
                $name = null;
            }else{
                $name = $filter;
            }
        }
        $repo = $this->getDoctrine()->getRepository(Photo::class);
        $list = $repo->getList($page,$rows,$showAll,$name);
        $count = $repo->getListCount($page,$rows,$showAll,$name);
        return $this->response->responseRowEntity($list,$count,200);
    }

    /**
     * @Route("media/gallery/upload", methods="POST")
     */
    publiC function addPhoto(Request $request){
        //todo role check
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

        $thumbPhoto = new File($path.".thumb.webp");
        $hdPhoto = new File($path.".hd.webp");

        unlink($path.".thumb.png");
        unlink($path.".hd.png");

        $thumbName = bin2hex(random_bytes(64)).".webp";
        $hdName = bin2hex(random_bytes(64)).".webp";

        $thumbPhoto->move(self::THUMB_FOLDER, $thumbName);
        $hdPhoto->move(self::HD_FOLDER, $hdName);

        $photo = new Photo();
        $photo->setHd($hdName);
        $photo->setThumb($thumbName);

        if($allowOrigin == "true"){
            exec("cwebp -q 100 ".$path." -o ".$path.".webp ");
            $originName = bin2hex(random_bytes(64)).".webp";
            $originPhoto = new File($path.".webp");
            $originPhoto->move(self::ORIGIN_FOLDER, $originName);
            $photo->setOrigin($originName);
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
