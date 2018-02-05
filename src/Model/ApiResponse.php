<?php
namespace App\Model;
use App\Model\Normalizer\GameNormalizer;
use App\Model\Normalizer\PhotoNormalizer;
use App\Model\Normalizer\UserNormalizer;
use App\Model\Normalizer\UuidNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiResponse {
    private $serializer;
    private $userSerializer;
    private $rawSerializer;
    const REQUIRED_LOGIN = 1001;
    const REQUIRED_AUTHORIZED = 1002;
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
        $uuidNormalizer = new UuidNormalizer();
        $gameNormalizer = new GameNormalizer();
        $this->rawSerializer = new Serializer([$uuidNormalizer,$dateNormalizer,$normalizer],[$encoder]);
        $this->serializer = new Serializer([$gameNormalizer,$photoNormalizer,$uuidNormalizer,$dateNormalizer,$userNormalizer,$normalizer],[$encoder]);
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
    function responseRawEntity($data,$code = 200){
        $data = json_decode($this->rawSerializer->serialize($data,"json"),TRUE);
        $json = new JsonResponse();
        $array = array("code"=>$code,"data"=>$data);
        $json->setData($array);
        return $json;
    }

}