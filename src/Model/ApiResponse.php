<?php
namespace App\Model;
use App\Model\Normalizer\PhotoNormalizer;
use App\Model\Normalizer\UserNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiResponse {
    private $serializer;
    private $userSerializer;
    private $rawSerializer;
    public function __construct()
    {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return null;
        });
        $dateNormalizer = new DateTimeNormalizer();
        $photoNormalizer = new PhotoNormalizer();
        $userNormalizer = new UserNormalizer();
        $this->serializer = new Serializer([$dateNormalizer,$photoNormalizer,$userNormalizer,$normalizer],[$encoder]);
        $this->rawSerializer = new Serializer([$dateNormalizer,$photoNormalizer,$normalizer],[$encoder]);
    }

    public function response($data,$code = 200){
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }
    public function responseRowEntity($data,$count,$code){
        $data = json_decode($this->rawSerializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        //$array = array("code"=>$code,"data"=>$data);
        $json->setData(array("rows"=>$data,"total"=>$count));
        return $json;
    }
    function responseEntity($data,$code = 200){
        $data = json_decode($this->serializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }
    function responseUser($data,$code = 200){
        $data = json_decode($this->rawSerializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }

}